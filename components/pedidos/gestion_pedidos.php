<?php
// ===============================
// Gestión de Pedidos - Panel Admin
// Tabla de pedidos con pago aprobado + notificación en plataforma
// Se incluye desde main.php (jQuery/DataTables/SweetAlert2 se cargan allí)
// ===============================
?>
<div class="card shadow-sm mb-4" id="cardGestionPedidos">
    <div class="card-header d-flex align-items-center justify-content-between bg-white">
        <h5 class="mb-0"><i class="bi bi-box-seam"></i> Gestión de Pedidos</h5>
        <div class="d-flex gap-2 align-items-center">
            <button class="btn btn-sm bg-indigo-dark" id="btnNotificaciones"><i class="bi bi-bell"></i> Notificar a</button>
            <button class="btn btn-sm bg-teal-dark" id="btnCostoEnvio"><i class="bi bi-truck"></i> Costo envío</button>
            <span class="badge bg-amber-dark" id="badgePorEnviar">Por enviar: 0</span>
        </div>
    </div>
    <div class="card-body w-100">
        <div class="table-responsive w-100">
            <table id="tablaPedidos" class="table table-striped table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th># Pedido</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Contacto</th>
                        <th>Ciudad / Dirección</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    const API_PEDIDOS = 'components/pedidos/api_pedidos.php';
    const API_ENVIAR = 'components/pedidos/enviar_pedido.php';
    const API_NOTIFICAR = 'components/pedidos/notificar_nuevo_pedido.php';
    const API_NOTIFY_USERS = 'components/pedidos/api_notify_users.php';
    const API_ENVIO_CONFIG = 'components/pedidos/api_envio_config.php';
    const POLL_MS = 10000;

    let lastMaxId = 0;
    let primeraCarga = true;
    let audioCtx = null;

    /* ---------- Utilidades ---------- */
    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function formatoCOP(valor) {
        return '$' + Number(valor || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 });
    }

    function formatoFecha(fecha) {
        if (!fecha) return '-';
        const d = new Date(fecha.replace(' ', 'T') + (fecha.includes('+') ? '' : '+00:00'));
        return d.toLocaleString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    /* Beep simple con Web Audio API */
    document.addEventListener('click', function initAudio() {
        if (!audioCtx) {
            try { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch (e) {}
        }
        document.removeEventListener('click', initAudio);
    });

    function sonarNotificacion() {
        if (!audioCtx) return;
        if (audioCtx.state === 'suspended') audioCtx.resume();
        [880, 1174].forEach(function (freq, i) {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.value = freq;
            gain.gain.setValueAtTime(0.001, audioCtx.currentTime + i * 0.18);
            gain.gain.exponentialRampToValueAtTime(0.25, audioCtx.currentTime + i * 0.18 + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + i * 0.18 + 0.15);
            osc.connect(gain).connect(audioCtx.destination);
            osc.start(audioCtx.currentTime + i * 0.18);
            osc.stop(audioCtx.currentTime + i * 0.18 + 0.16);
        });
    }

    /* ---------- Tabla ---------- */
    const tabla = $('#tablaPedidos').DataTable({
        responsive: true,
        order: [[1, 'asc']],
        language: { url: 'controller/datatable_esp.json' },
        pagingType: 'simple',
        columnDefs: [{ targets: [8], orderable: false }]
    });

    function badgeEstado(status) {
        switch (status) {
            case 'processing': return '<span class="badge bg-amber-dark">Por enviar</span>';
            case 'shipped': return '<span class="badge bg-indigo-dark">Enviado</span>';
            case 'delivered': return '<span class="badge bg-lime-dark">Entregado</span>';
            case 'completed': return '<span class="badge bg-lime-dark">Completado</span>';
            default: return '<span class="badge bg-silver text-dark">' + escapeHtml(status) + '</span>';
        }
    }

    function celdaAccion(p) {
        if (p.tracking_number) {
            return '<button class="btn btn-sm bg-indigo-dark btn-detalle-envio" data-id="' + p.id + '">' +
                '<i class="bi bi-eye"></i> Ver detalle</button>';
        }
        if (p.status === 'processing') {
            return '<button class="btn btn-sm bg-magenta-dark btn-enviar" data-id="' + p.id + '">' +
                '<i class="bi bi-truck"></i> Marcar enviado</button>';
        }
        return '<span class="text-muted small">—</span>';
    }

    function cargarPedidos() {
        return fetch(API_PEDIDOS + '?action=listar')
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success) return;
                tabla.clear();
                let porEnviar = 0;
                res.data.forEach(function (p) {
                    if (p.status === 'processing' && !p.tracking_number) porEnviar++;
                    tabla.row.add([
                        '<strong>' + escapeHtml(p.order_number) + '</strong>',
                        escapeHtml(p.created_at),
                        escapeHtml(p.customer_name),
                        '<div class="small">' + escapeHtml(p.customer_email) + '<br>' + escapeHtml(p.customer_phone || '') + '</div>',
                        '<div class="small">' + escapeHtml((p.shipping_city || '') + (p.shipping_department ? ', ' + p.shipping_department : '')) +
                            '<br>' + escapeHtml(p.shipping_address || '') + '</div>',
                        '<div class="small">' + escapeHtml(p.items || '') + '</div>',
                        formatoCOP(p.total),
                        badgeEstado(p.status),
                        celdaAccion(p)
                    ]);
                });
                tabla.draw(false);
                $('#badgePorEnviar').text('Por enviar: ' + porEnviar);
                if (res.max_id > lastMaxId) lastMaxId = res.max_id;
                primeraCarga = false;
            })
            .catch(function (err) { console.error('Error cargando pedidos:', err); });
    }

    /* ---------- Polling ---------- */
    function verificarNuevos() {
        if (primeraCarga) return;
        fetch(API_PEDIDOS + '?action=nuevos&last_id=' + lastMaxId)
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success || res.count <= 0) return;
                lastMaxId = res.max_id;
                sonarNotificacion();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.count === 1 ? '¡Nuevo pedido pagado!' : '¡' + res.count + ' pedidos nuevos pagados!',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
                cargarPedidos();

                // Notificar a los usuarios asignados por email (fuego y olvido)
                var formNotif = new FormData();
                formNotif.append('order_id', res.max_id);
                navigator.sendBeacon(API_NOTIFICAR, formNotif);
            })
            .catch(function () {});
    }

    /* ---------- Cargar items para tabla dentro de modales ---------- */
    function itemsATabla(items) {
        var html = '<table class="table table-sm table-striped small"><thead><tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr></thead><tbody>';
        items.forEach(function (i) {
            html += '<tr><td>' + escapeHtml(i.product_name) + '</td><td class="text-center">' + i.quantity + '</td>' +
                '<td class="text-end">' + formatoCOP(i.unit_price) + '</td><td class="text-end">' + formatoCOP(i.total) + '</td></tr>';
        });
        html += '</tbody></table>';
        return html;
    }

    /* =============================================
     * SWEETALERT: MARCAR COMO ENVIADO (por enviar)
     * ============================================= */
    $('#tablaPedidos').on('click', '.btn-enviar', function () {
        const orderId = $(this).data('id');

        fetch(API_PEDIDOS + '?action=detalle&order_id=' + orderId)
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success) { Swal.fire({ icon: 'error', title: 'Error', text: res.message }); return; }
                const o = res.order;
                const items = res.items;

                Swal.fire({
                    title: '<i class="bi bi-truck"></i> Despachar pedido',
                    html:
                        '<div class="text-start" style="font-size:15px">' +
                        '<div class="row g-2 mb-2">' +
                        '<div class="col-6"><div class="card border-indigo-dark"><div class="card-header bg-indigo-light py-1 fw-bold" style="font-size:15px">Datos del cliente</div>' +
                        '<div class="card-body py-2">' +
                        '<div class="fw-bold">' + escapeHtml(o.customer_name) + '</div>' +
                        '<div>' + escapeHtml(o.customer_email) + '</div>' +
                        '<div>' + escapeHtml(o.customer_phone || '') + '</div>' +
                        (o.customer_document_type ? '<div class="text-muted">' + escapeHtml(o.customer_document_type) + ': ' + escapeHtml(o.customer_document_number || '') + '</div>' : '') +
                        '</div></div></div>' +
                        '<div class="col-6"><div class="card border-amber-dark"><div class="card-header bg-amber-light py-1 fw-bold" style="font-size:15px">Dirección de envío</div>' +
                        '<div class="card-body py-2">' +
                        (o.shipping_department ? '<div><strong>Dpto:</strong> ' + escapeHtml(o.shipping_department) + '</div>' : '') +
                        '<div><strong>Ciudad:</strong> ' + escapeHtml(o.shipping_city || '-') + '</div>' +
                        '<div>' + escapeHtml(o.shipping_address || '') + '</div>' +
                        (o.shipping_address_detail ? '<div>' + escapeHtml(o.shipping_address_detail) + '</div>' : '') +
                        (o.shipping_postal_code ? '<div><strong>CP:</strong> ' + escapeHtml(o.shipping_postal_code) + '</div>' : '') +
                        '</div></div></div>' +
                        '</div>' +
                        '<div class="card border-lime-dark mb-2"><div class="card-header bg-lime-light py-1 fw-bold" style="font-size:15px">Contenido</div>' +
                        '<div class="card-body p-1">' + itemsATabla(items) + '<div class="text-end fw-bold" style="font-size:16px">TOTAL: ' + formatoCOP(o.total) + '</div></div></div>' +
                        '<div class="card border-magenta-dark"><div class="card-header bg-magenta-light py-1 fw-bold" style="font-size:15px">Registrar envío</div>' +
                        '<div class="card-body p-2">' +
                        '<input type="text" id="swalCarrier" class="swal2-input mb-2" placeholder="Transportadora (ej: Servientrega)" style="font-size:15px">' +
                        '<input type="text" id="swalTracking" class="swal2-input mb-2" placeholder="Número de guía" style="font-size:15px">' +
                        '<textarea id="swalNotas" class="swal2-textarea" placeholder="Notas (opcional)" style="font-size:15px;height:60px"></textarea>' +
                        '</div></div>' +
                        '</div>',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-send"></i> Confirmar envío',
                    confirmButtonColor: '#ec008c',
                    cancelButtonText: 'Cancelar',
                    width: '900px',
                    customClass: { popup: 'text-start' },
                    preConfirm: function () {
                        const carrier = document.getElementById('swalCarrier').value.trim();
                        const tracking = document.getElementById('swalTracking').value.trim();
                        const notes = document.getElementById('swalNotas').value.trim();
                        if (!carrier || !tracking) {
                            Swal.showValidationMessage('Transportadora y guía son obligatorios');
                            return false;
                        }
                        return { carrier: carrier, tracking: tracking, notes: notes };
                    }
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    Swal.fire({ title: 'Registrando envío...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

                    const formData = new FormData();
                    formData.append('order_id', orderId);
                    formData.append('carrier', result.value.carrier);
                    formData.append('tracking_number', result.value.tracking);
                    formData.append('notes', result.value.notes);

                    fetch(API_ENVIAR, { method: 'POST', body: formData })
                        .then(function (r) { return r.json(); })
                        .then(function (res) {
                            if (res.success) {
                                let icon = 'success';
                                if (!res.email_sent && !res.pdf_ok) icon = 'warning';
                                else if (!res.email_sent) icon = 'warning';
                                Swal.fire({ icon: icon, title: icon === 'success' ? 'Pedido enviado' : 'Envío registrado', text: res.message });
                                cargarPedidos();
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                            }
                        })
                        .catch(function (err) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo registrar el envío: ' + err });
                        });
                });
            })
            .catch(function (err) { Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar el pedido: ' + err }); });
    });

    /* =============================================
     * SWEETALERT: VER DETALLE (pedidos enviados)
     * ============================================= */
    $('#tablaPedidos').on('click', '.btn-detalle-envio', function () {
        const orderId = $(this).data('id');

        Swal.fire({ title: 'Cargando...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

        fetch(API_PEDIDOS + '?action=detalle_envio&order_id=' + orderId)
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success) { Swal.fire({ icon: 'error', title: 'Error', text: res.message }); return; }
                const d = res.data;
                const items = res.items || [];

                const auditado = d.admin_username ? '<span class="badge bg-lime-dark" style="font-size:14px">Confirmado por</span> <strong>' + escapeHtml(d.admin_username) + '</strong><br><span class="text-muted">' + formatoFecha(d.audit_at) + '</span>' : '';

                const invoiceLink = d.receipt_filename
                    ? '<a href="uploads/facturas/' + escapeHtml(d.receipt_filename) + '" target="_blank" class="btn btn-sm bg-magenta-dark mt-2" style="font-size:14px"><i class="bi bi-file-pdf"></i> Ver recibo PDF</a>'
                    : '<span class="text-muted">Recibo no disponible</span>';

                Swal.fire({
                    title: '<i class="bi bi-truck"></i> Pedido ' + escapeHtml(d.order_number),
                    html:
                        '<div class="text-start" style="font-size:15px">' +
                        '<div class="card border-indigo-dark mb-2"><div class="card-header bg-indigo-light py-1 fw-bold" style="font-size:15px">Datos de envío</div>' +
                        '<div class="card-body py-2">' +
                        '<div><strong>Transportadora:</strong> ' + escapeHtml(d.carrier) + '</div>' +
                        '<div><strong>Guía:</strong> <span class="fw-bold">' + escapeHtml(d.tracking_number) + '</span></div>' +
                        '<div class="text-muted">Despachado: ' + formatoFecha(d.shipped_at) + '</div>' +
                        (d.email_sent == 1 ? '<span class="badge bg-lime-dark" style="font-size:13px"><i class="bi bi-envelope-check"></i> Correo enviado</span>' : '') +
                        '</div></div>' +
                        (auditado ? '<div class="mb-2">' + auditado + '</div>' : '') +
                        '<div class="card border-lime-dark mb-2"><div class="card-header bg-lime-light py-1 fw-bold" style="font-size:15px">Productos</div>' +
                        '<div class="card-body p-1">' + itemsATabla(items) + '<div class="text-end fw-bold" style="font-size:16px">TOTAL: ' + formatoCOP(d.total) + '</div></div></div>' +
                        '<div class="text-center">' + invoiceLink + '</div>' +
                        '</div>',
                    width: '900px',
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: { popup: 'text-start' }
                });
            })
            .catch(function (err) { Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar: ' + err }); });
    });

    /* =============================================
     * MODAL: ASIGNAR NOTIFICACIONES (switches)
     * ============================================= */
    $('#btnNotificaciones').on('click', function () {
        Swal.fire({ title: 'Cargando usuarios...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

        fetch(API_NOTIFY_USERS + '?action=listar')
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success) { Swal.fire({ icon: 'error', title: 'Error', text: res.message }); return; }

                var rows = '';
                res.data.forEach(function (u) {
                    var checked = u.notificaciones_activas == 1 ? 'checked' : '';
                    rows += '<tr>' +
                        '<td style="font-size:14px">' + escapeHtml(u.username) + '</td>' +
                        '<td style="font-size:14px">' + escapeHtml(u.nombre) + '</td>' +
                        '<td style="font-size:13px">' + escapeHtml(u.email) + '</td>' +
                        '<td class="text-center">' +
                        '<div class="form-check form-switch d-inline-block">' +
                        '<input class="form-check-input switch-notify" type="checkbox" role="switch" data-user-id="' + u.id + '" ' + checked + ' style="cursor:pointer;transform:scale(1.3)">' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                });

                Swal.fire({
                    title: '<i class="bi bi-bell"></i> Usuarios que reciben notificaciones',
                    html:
                        '<div class="text-start" style="font-size:14px">' +
                        '<p class="text-muted small">Activa el interruptor para que el usuario reciba un correo cada vez que llegue un pedido nuevo pagado.</p>' +
                        '<div style="max-height:400px;overflow-y:auto">' +
                        '<table class="table table-sm table-striped mb-0" style="font-size:14px">' +
                        '<thead><tr><th>Username</th><th>Nombre</th><th>Email</th><th class="text-center">Activo</th></tr></thead>' +
                        '<tbody>' + rows + '</tbody>' +
                        '</table>' +
                        '</div>' +
                        '</div>',
                    width: '850px',
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: { popup: 'text-start' },
                    didOpen: function () {
                        // Evento para los switches
                        document.querySelectorAll('.switch-notify').forEach(function (sw) {
                            sw.addEventListener('change', function () {
                                var uid = this.getAttribute('data-user-id');
                                var active = this.checked ? 1 : 0;
                                var formData = new FormData();
                                formData.append('action', 'toggle');
                                formData.append('user_id', uid);
                                formData.append('active', active);
                                fetch(API_NOTIFY_USERS, { method: 'POST', body: formData })
                                    .then(function (r) { return r.json(); })
                                    .then(function (res) {
                                        if (!res.success) {
                                            Swal.fire({ icon: 'error', title: 'Error', text: res.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                                        }
                                    });
                            });
                        });
                    }
                });
            })
            .catch(function (err) { Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Error al cargar usuarios' }); });
    });

    /* =============================================
     * MODAL: CONFIGURAR COSTO DE ENVÍO
     * ============================================= */
    $('#btnCostoEnvio').on('click', function () {
        Swal.fire({ title: 'Cargando...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

        fetch(API_ENVIO_CONFIG + '?action=get')
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success) { Swal.fire({ icon: 'error', title: 'Error', text: res.message }); return; }

                var valorActual = res.valor;

                Swal.fire({
                    title: '<i class="bi bi-truck"></i> Costo de envío',
                    html:
                        '<div class="text-start" style="font-size:15px">' +
                        '<p class="text-muted">Valor actual: <strong>$' + valorActual.toLocaleString('es-CO', { maximumFractionDigits: 0 }) + '</strong></p>' +
                        '<label class="fw-bold">Nuevo valor:</label>' +
                        '<input type="number" id="swalCostoEnvio" class="swal2-input" value="' + valorActual + '" min="0" step="100" style="font-size:15px">' +
                        '</div>',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-check-lg"></i> Guardar',
                    confirmButtonColor: '#006d68',
                    cancelButtonText: 'Cancelar',
                    width: '500px',
                    customClass: { popup: 'text-start' },
                    preConfirm: function () {
                        var nuevo = parseFloat(document.getElementById('swalCostoEnvio').value);
                        if (isNaN(nuevo) || nuevo < 0) {
                            Swal.showValidationMessage('Ingresa un valor válido');
                            return false;
                        }
                        return nuevo;
                    }
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    var nuevoValor = result.value;

                    Swal.fire({ title: 'Guardando...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

                    var formData = new FormData();
                    formData.append('action', 'update');
                    formData.append('valor', nuevoValor);

                    fetch(API_ENVIO_CONFIG, { method: 'POST', body: formData })
                        .then(function (r) { return r.json(); })
                        .then(function (res) {
                            if (res.success) {
                                Swal.fire({ icon: 'success', title: 'Actualizado', text: res.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                            }
                        })
                        .catch(function (err) { Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Error al guardar' }); });
                });
            })
            .catch(function (err) { Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Error al cargar' }); });
    });

    /* ---------- Inicio ---------- */
    cargarPedidos().then(function () {
        setInterval(verificarNuevos, POLL_MS);
    });
});
</script>
