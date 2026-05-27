<!-- ============================================================
     products_categories.php — Componente incluido en listProducts.php
     Sin etiquetas html/head/body (ya estan en el padre)
     AJAX → api_categories.php | api_products.php | api_product_images.php
     ============================================================ -->

<!-- ── NavTabs ─────────────────────────────────────────────────── -->
<ul class="nav nav-tabs mb-3" id="ecomTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="tab-cat-btn"
            data-bs-toggle="tab" data-bs-target="#tab-cat"
            type="button" role="tab">
            <i class="bi bi-tags-fill me-1"></i>Categorías
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-prod-btn"
            data-bs-toggle="tab" data-bs-target="#tab-prod"
            type="button" role="tab">
            <i class="bi bi-box-seam-fill me-1"></i>Productos
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-stock-btn"
            data-bs-toggle="tab" data-bs-target="#tab-stock"
            type="button" role="tab">
            <i class="bi bi-clipboard2-data-fill me-1"></i>Stock e Inventario
        </button>
    </li>
</ul>

<div class="tab-content" id="ecomTabContent">

    <!-- ══════════════ TAB: CATEGORÍAS ══════════════ -->
    <div class="tab-pane fade show active" id="tab-cat" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-secondary"><i class="bi bi-tags me-1"></i>Listado de Categorías</h5>
            <button class="btn btn-success btn-sm" onclick="abrirModalNuevaCategoria()">
                <i class="bi bi-plus-lg me-1"></i>Nueva Categoría
            </button>
        </div>

        <div class="table-responsive">
            <table id="tablaCategorias" class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th class="text-center"># Productos</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="bodyCategorias">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Cargando...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══════════════ TAB: PRODUCTOS ══════════════ -->
    <div class="tab-pane fade" id="tab-prod" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-secondary"><i class="bi bi-box-seam me-1"></i>Listado de Productos</h5>
            <button class="btn btn-success btn-sm" onclick="abrirModalNuevoProducto()">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
            </button>
        </div>

        <!-- Filtros -->
        <div class="row g-2 mb-3 align-items-center" id="filtros-form">
            <div class="col-auto">
                <select id="filtroCat" class="form-select form-select-sm" onchange="filtrarProductos()">
                    <option value="">Todas las categorias</option>
                </select>
            </div>
            <div class="col-auto">
                <select id="filtroEst" class="form-select form-select-sm" onchange="filtrarProductos()">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
            <div class="col-auto">
                <select id="filtroDesc" class="form-select form-select-sm" onchange="filtrarProductos()">
                    <option value="">Destacado: todos</option>
                    <option value="1">Solo destacados</option>
                    <option value="0">No destacados</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary btn-sm" onclick="limpiarFiltros()">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tablaProductos" class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Imagen</th>
                        <th>Serie</th>
                        <th>Nombre</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Dest.</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="bodyProductos">
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Cargando...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- ═══════════════ TAB: STOCK E INVENTARIO ═══════════════ -->
    <div class="tab-pane fade" id="tab-stock" role="tabpanel">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-secondary"><i class="bi bi-clipboard2-data me-1"></i>Control de Inventario</h5>
            <button class="btn btn-outline-secondary btn-sm" onclick="cargarStock()">
                <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
            </button>
        </div>

        <div class="row g-3 mb-4" id="stockResumen">
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fs-2 fw-bold text-secondary">—</div>
                    <div class="text-muted small">Cargando...</div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="tablaStock" class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Serie</th>
                        <th>Producto</th>
                        <th>Categoria</th>
                        <th>Stock Actual</th>
                        <th>Stock Minimo</th>
                        <th>Estado Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="bodyStock">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Cargando...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════
     ESTILOS LOCALES
══════════════════════════════════════════════════════════════════ -->
<style>
    #ecomTab .nav-link {
        color: #495057;
    }

    #ecomTab .nav-link.active {
        color: #fff;
        background-color: #198754;
        border-color: #198754;
    }

    .prod-thumb {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .prod-no-img {
        width: 56px;
        height: 56px;
        background: #f1f3f5;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 1.4rem;
        border: 1px solid #dee2e6;
    }

    /* Preview de imagen en SweetAlert */
    #previewImgCat,
    #previewImgProd {
        max-width: 100%;
        max-height: 120px;
        margin-top: 6px;
        border-radius: 6px;
        display: none;
        object-fit: cover;
    }

    .serie-badge {
        font-family: monospace;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 0.83em;
        letter-spacing: 0.05em;
    }

    .gal-thumb {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        display: block;
    }

    .stock-ok {
        color: #198754;
        font-weight: 700;
    }

    .stock-bajo {
        color: #ffc107;
        font-weight: 700;
    }

    .stock-cero {
        color: #dc3545;
        font-weight: 700;
    }

    .attr-badge-wrap {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        margin: 2px;
    }

    #stockResumen .card {
        border-radius: 10px;
    }
</style>

<!-- ════════════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════════════ -->
<script>
    /* ─── Estado global ──────────────────────────────────────────────── */
    let dtCategorias = null;
    let dtProductos = null;
    let dtStock = null;
    let categoriasList = []; // cache para dropdown de productos

    /* ─── Rutas de API ──────────────────────────────────────────────────────────────── */
    const API_CAT = 'components/products/api_categories.php';
    const API_PROD = 'components/products/api_products.php';
    const API_IMGS = 'components/products/api_product_images.php';
    const API_ATTRS = 'components/products/api_product_attrs.php';
    const API_STOCK = 'components/products/api_stock.php';

    /* ─── Formateo de precio ─────────────────────────────────────────── */
    function formatPrecio(val) {
        return '$' + parseFloat(val).toLocaleString('es-CO');
    }

    /* ─── Badge de estado ────────────────────────────────────────────── */
    function badgeEstado(estado) {
        return estado === 'activo' ?
            '<span class="badge bg-success">Activo</span>' :
            '<span class="badge bg-secondary">Inactivo</span>';
    }

    /* ══════════════ CATEGORÍAS ══════════════ */

    function cargarCategorias() {
        fetch(API_CAT + '?action=get')
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                categoriasList = res.data;

                if (dtCategorias) {
                    dtCategorias.destroy();
                    dtCategorias = null;
                }

                // Rellenar dropdown de categorias en filtros de productos
                const filtroCat = document.getElementById('filtroCat');
                if (filtroCat) {
                    filtroCat.innerHTML = '<option value="">Todas las categorias</option>' +
                        res.data.map(c => `<option value="${escAttr(c.nombre)}">${escHtml(c.nombre)}</option>`).join('');
                }

                let filas = '';
                res.data.forEach((c, i) => {
                    const total = parseInt(c.total_productos || 0);
                    filas += `
                <tr>
                    <td class="text-center">${i + 1}</td>
                    <td>${escHtml(c.nombre)}</td>
                    <td>${escHtml(c.descripcion || '—')}</td>
                    <td class="text-center">
                        <span class="badge ${total > 0 ? 'bg-primary' : 'bg-secondary'}">${total}</span>
                    </td>
                    <td class="text-center">${badgeEstado(c.estado)}</td>
                    <td class="text-center">${c.fecha_creacion ? c.fecha_creacion.substring(0,10) : '—'}</td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm me-1"
                            onclick="editarCategoria(${c.id},'${escAttr(c.nombre)}','${escAttr(c.descripcion||'')}','${c.estado}')">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-danger btn-sm"
                            onclick="eliminarCategoria(${c.id},'${escAttr(c.nombre)}')">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>`;
                });

                document.getElementById('bodyCategorias').innerHTML = filas || '<tr><td colspan="7" class="text-center text-muted">Sin categorias</td></tr>';

                dtCategorias = $('#tablaCategorias').DataTable({
                    responsive: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    pagingType: 'simple',
                    columnDefs: [{
                        orderable: false,
                        targets: [6]
                    }]
                });
            });
    }

    function abrirModalNuevaCategoria() {
        Swal.fire({
            title: '<i class="bi bi-tag-fill text-success me-2"></i>Nueva Categoría',
            html: formCategoria(),
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-save me-1"></i>Guardar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
            focusConfirm: false,
            didOpen: () => {
                document.getElementById('swal-file-cat').addEventListener('change', function() {
                    previewFile(this, 'previewImgCat');
                });
            },
            preConfirm: () => {
                const nombre = document.getElementById('swal-cat-nombre').value.trim();
                if (!nombre) {
                    Swal.showValidationMessage('El nombre es requerido');
                    return false;
                }
                const fd = new FormData();
                fd.append('action', 'create');
                fd.append('nombre', nombre);
                fd.append('descripcion', document.getElementById('swal-cat-desc').value);
                fd.append('estado', document.getElementById('swal-cat-estado').value);
                const file = document.getElementById('swal-file-cat').files[0];
                if (file) fd.append('imagen', file);
                return fetch(API_CAT, {
                    method: 'POST',
                    body: fd
                }).then(r => r.json());
            }
        }).then(result => {
            if (result.isConfirmed && result.value?.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Creada!',
                    text: result.value.message,
                    timer: 1800,
                    showConfirmButton: false
                });
                cargarCategorias();
            } else if (result.isConfirmed && result.value) {
                Swal.fire('Error', result.value.message, 'error');
            }
        });
    }

    function editarCategoria(id, nombre, descripcion, estado) {
        Swal.fire({
            title: '<i class="bi bi-pencil-fill text-warning me-2"></i>Editar Categoría',
            html: formCategoria({
                nombre,
                descripcion,
                estado
            }),
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-save me-1"></i>Actualizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ffc107',
            focusConfirm: false,
            didOpen: () => {
                document.getElementById('swal-file-cat').addEventListener('change', function() {
                    previewFile(this, 'previewImgCat');
                });
            },
            preConfirm: () => {
                const nombre = document.getElementById('swal-cat-nombre').value.trim();
                if (!nombre) {
                    Swal.showValidationMessage('El nombre es requerido');
                    return false;
                }
                const fd = new FormData();
                fd.append('action', 'update');
                fd.append('id', id);
                fd.append('nombre', nombre);
                fd.append('descripcion', document.getElementById('swal-cat-desc').value);
                fd.append('estado', document.getElementById('swal-cat-estado').value);
                const file = document.getElementById('swal-file-cat').files[0];
                if (file) fd.append('imagen', file);
                return fetch(API_CAT, {
                    method: 'POST',
                    body: fd
                }).then(r => r.json());
            }
        }).then(result => {
            if (result.isConfirmed && result.value?.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizada!',
                    text: result.value.message,
                    timer: 1800,
                    showConfirmButton: false
                });
                cargarCategorias();
            } else if (result.isConfirmed && result.value) {
                Swal.fire('Error', result.value.message, 'error');
            }
        });
    }

    function eliminarCategoria(id, nombre) {
        Swal.fire({
            title: '¿Eliminar categoría?',
            html: `<p>Estás a punto de eliminar <strong>${escHtml(nombre)}</strong>.<br>Esta acción no se puede deshacer.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(result => {
            if (!result.isConfirmed) return;
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            fetch(API_CAT, {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        cargarCategorias();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
        });
    }

    function formCategoria(data = {}) {
        const estadoActivo = (data.estado === 'inactivo') ? '' : 'selected';
        const estadoInactivo = (data.estado === 'inactivo') ? 'selected' : '';
        return `
    <div class="text-start">
        <div class="mb-2">
            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
            <input id="swal-cat-nombre" class="form-control" type="text"
                value="${escAttr(data.nombre || '')}" placeholder="Ej: Accesorios">
        </div>
        <div class="mb-2">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="swal-cat-desc" class="form-control" rows="2"
                placeholder="Descripción opcional">${escHtml(data.descripcion || '')}</textarea>
        </div>
        <div class="mb-2">
            <label class="form-label fw-semibold">Estado</label>
            <select id="swal-cat-estado" class="form-select">
                <option value="activo"   ${estadoActivo}>Activo</option>
                <option value="inactivo" ${estadoInactivo}>Inactivo</option>
            </select>
        </div>
    </div>`;
    }

    /* ══════════════ PRODUCTOS ══════════════ */

    function cargarProductos(filtros = {}) {
        const params = new URLSearchParams(filtros);
        const qs = [...params].length > 0 ? '&' + params.toString() : '';
        fetch(API_PROD + '?action=get' + qs)
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;

                if (dtProductos) {
                    dtProductos.destroy();
                    dtProductos = null;
                }

                let filas = '';
                res.data.forEach(p => {
                    const imgHtml = p.imagen ?
                        `<img src="${escAttr(p.imagen)}" class="prod-thumb" alt="${escAttr(p.nombre)}">` :
                        `<div class="prod-no-img mx-auto"><i class="bi bi-image"></i></div>`;

                    const precioHtml = p.precio_oferta && parseFloat(p.precio_oferta) > 0 ?
                        `<small class="text-muted text-decoration-line-through d-block">${formatPrecio(p.precio)}</small>
                       <span class="fw-bold text-danger">${formatPrecio(p.precio_oferta)}</span>
                       <span class="badge bg-danger ms-1">-${Math.round((1 - p.precio_oferta / p.precio) * 100)}%</span>` :
                        formatPrecio(p.precio);

                    const stockVal = parseInt(p.stock);
                    const stockMin = parseInt(p.stock_minimo || 5);
                    const stockHtml = stockVal === 0 ?
                        `<span class="fw-bold text-danger">${stockVal}</span> <span class="badge bg-danger">Sin stock</span>` :
                        stockVal <= stockMin ?
                        `${stockVal} <span class="badge bg-warning text-dark">Bajo</span>` :
                        `<span class="fw-bold text-success">${stockVal}</span>`;

                    const destHtml = parseInt(p.destacado) === 1 ?
                        `<i class="bi bi-star-fill text-warning fs-5" style="cursor:pointer" title="Quitar destacado"
                          onclick="toggleDestacado(${p.id},0)"></i>` :
                        `<i class="bi bi-star text-muted fs-5" style="cursor:pointer" title="Marcar destacado"
                          onclick="toggleDestacado(${p.id},1)"></i>`;

                    filas += `
                <tr>
                    <td class="text-center">${imgHtml}</td>
                    <td class="text-center"><span class="serie-badge">${escHtml(p.numero_serie || '—')}</span></td>
                    <td>${escHtml(p.nombre)}</td>
                    <td>${escHtml(p.categoria || '—')}</td>
                    <td class="text-end">${precioHtml}</td>
                    <td class="text-center">${stockHtml}</td>
                    <td class="text-center">${destHtml}</td>
                    <td class="text-center">${badgeEstado(p.estado)}</td>
                    <td class="text-center">
                        <button class="btn btn-secondary btn-sm me-1" title="Atributos (colores/tallas)"
                            onclick="gestionarAtributos(${p.id},'${escAttr(p.nombre)}')">
                            <i class="bi bi-sliders"></i>
                        </button>
                        <button class="btn btn-info btn-sm me-1" title="Galeria de imagenes"
                            onclick="gestionarGaleria(${p.id},'${escAttr(p.nombre)}')">
                            <i class="bi bi-images"></i>
                        </button>
                        <button class="btn btn-success btn-sm me-1" title="Duplicar producto"
                            onclick="duplicarProducto(${p.id},'${escAttr(p.nombre)}')">
                            <i class="bi bi-files"></i>
                        </button>
                        <button class="btn btn-warning btn-sm me-1" title="Editar"
                            onclick='editarProducto(${JSON.stringify(p)})'>
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" title="Eliminar"
                            onclick="eliminarProducto(${p.id},'${escAttr(p.nombre)}')">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>`;
                });

                document.getElementById('bodyProductos').innerHTML = filas || '<tr><td colspan="9" class="text-center text-muted">Sin productos</td></tr>';

                dtProductos = $('#tablaProductos').DataTable({
                    responsive: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    pagingType: 'simple',
                    columnDefs: [{
                        orderable: false,
                        targets: [0, 6, 8]
                    }]
                });
            });
    }

    function abrirModalNuevoProducto() {
        Swal.fire({
            title: '<i class="bi bi-box-seam-fill text-success me-2"></i>Nuevo Producto',
            html: formProducto(),
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-save me-1"></i>Guardar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
            width: 640,
            focusConfirm: false,
            didOpen: () => {
                document.getElementById('swal-file-prod').addEventListener('change', function() {
                    previewFile(this, 'previewImgProd');
                });
            },
            preConfirm: () => {
                const nombre = document.getElementById('swal-prod-nombre').value.trim();
                const precio = parseFloat(document.getElementById('swal-prod-precio').value);
                if (!nombre) {
                    Swal.showValidationMessage('El nombre es requerido');
                    return false;
                }
                if (isNaN(precio)) {
                    Swal.showValidationMessage('El precio debe ser un numero');
                    return false;
                }

                const fd = new FormData();
                fd.append('action', 'create');
                fd.append('nombre', nombre);
                fd.append('descripcion', document.getElementById('swal-prod-desc').value);
                fd.append('precio', precio);
                const po = document.getElementById('swal-prod-precio-oferta').value;
                if (po) fd.append('precio_oferta', parseFloat(po));
                fd.append('stock', document.getElementById('swal-prod-stock').value);
                fd.append('stock_minimo', document.getElementById('swal-prod-stock-min').value);
                fd.append('categoria', document.getElementById('swal-prod-cat').value);
                fd.append('estado', document.getElementById('swal-prod-estado').value);
                if (document.getElementById('swal-prod-destacado').checked) fd.append('destacado', '1');
                const file = document.getElementById('swal-file-prod').files[0];
                if (file) fd.append('imagen', file);
                return fetch(API_PROD, {
                    method: 'POST',
                    body: fd
                }).then(r => r.json());
            }
        }).then(result => {
            if (result.isConfirmed && result.value?.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Creado!',
                    text: `N.° Serie: ${result.value.numero_serie}`,
                    timer: 2500,
                    showConfirmButton: false
                });
                cargarProductos();
            } else if (result.isConfirmed && result.value) {
                Swal.fire('Error', result.value.message, 'error');
            }
        });
    }

    function editarProducto(p) {
        Swal.fire({
            title: '<i class="bi bi-pencil-fill text-warning me-2"></i>Editar Producto',
            html: formProducto(p),
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-save me-1"></i>Actualizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ffc107',
            width: 640,
            focusConfirm: false,
            didOpen: () => {
                document.getElementById('swal-file-prod').addEventListener('change', function() {
                    previewFile(this, 'previewImgProd');
                });
                // Mostrar imagen actual si existe
                if (p.imagen) {
                    const prev = document.getElementById('previewImgProd');
                    prev.src = p.imagen;
                    prev.style.display = 'block';
                }
            },
            preConfirm: () => {
                const nombre = document.getElementById('swal-prod-nombre').value.trim();
                const precio = parseFloat(document.getElementById('swal-prod-precio').value);
                if (!nombre) {
                    Swal.showValidationMessage('El nombre es requerido');
                    return false;
                }
                if (isNaN(precio)) {
                    Swal.showValidationMessage('El precio debe ser un numero');
                    return false;
                }

                const fd = new FormData();
                fd.append('action', 'update');
                fd.append('id', p.id);
                fd.append('nombre', nombre);
                fd.append('descripcion', document.getElementById('swal-prod-desc').value);
                fd.append('precio', precio);
                const po = document.getElementById('swal-prod-precio-oferta').value;
                if (po) fd.append('precio_oferta', parseFloat(po));
                fd.append('stock', document.getElementById('swal-prod-stock').value);
                fd.append('stock_minimo', document.getElementById('swal-prod-stock-min').value);
                fd.append('categoria', document.getElementById('swal-prod-cat').value);
                fd.append('estado', document.getElementById('swal-prod-estado').value);
                if (document.getElementById('swal-prod-destacado').checked) fd.append('destacado', '1');
                const file = document.getElementById('swal-file-prod').files[0];
                if (file) fd.append('imagen', file);
                return fetch(API_PROD, {
                    method: 'POST',
                    body: fd
                }).then(r => r.json());
            }
        }).then(result => {
            if (result.isConfirmed && result.value?.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: result.value.message,
                    timer: 1800,
                    showConfirmButton: false
                });
                cargarProductos();
            } else if (result.isConfirmed && result.value) {
                Swal.fire('Error', result.value.message, 'error');
            }
        });
    }

    function eliminarProducto(id, nombre) {
        Swal.fire({
            title: '¿Eliminar producto?',
            html: `<p>Estás a punto de eliminar <strong>${escHtml(nombre)}</strong>.<br>Esta acción no se puede deshacer.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(result => {
            if (!result.isConfirmed) return;
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            fetch(API_PROD, {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        cargarProductos();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
        });
    }

    function formProducto(data = {}) {
        const catOptions = categoriasList.map(c =>
            `<option value="${escAttr(c.nombre)}" ${data.categoria === c.nombre ? 'selected' : ''}>${escHtml(c.nombre)}</option>`
        ).join('');

        const estadoActivo = (data.estado === 'inactivo') ? '' : 'selected';
        const estadoInactivo = (data.estado === 'inactivo') ? 'selected' : '';
        const destChecked = parseInt(data.destacado) === 1 ? 'checked' : '';

        const serieHtml = data.numero_serie ?
            `<div class="mb-2">
               <label class="form-label fw-semibold text-muted small">N.° de Serie (autogenerado)</label>
               <input class="form-control form-control-sm font-monospace" type="text"
                   value="${escAttr(data.numero_serie)}" readonly disabled>
           </div>` :
            '';

        return `
    <div class="text-start">
        ${serieHtml}
        <div class="row g-2">
            <div class="col-12">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input id="swal-prod-nombre" class="form-control" type="text"
                    value="${escAttr(data.nombre || '')}" placeholder="Ej: Paraguas Poder Down">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Descripcion</label>
                <textarea id="swal-prod-desc" class="form-control" rows="2"
                    placeholder="Descripcion del producto">${escHtml(data.descripcion || '')}</textarea>
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Precio (COP) <span class="text-danger">*</span></label>
                <input id="swal-prod-precio" class="form-control" type="number" min="0" step="100"
                    value="${escAttr(String(data.precio || '0'))}" placeholder="85000">
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Precio oferta (COP)</label>
                <input id="swal-prod-precio-oferta" class="form-control" type="number" min="0" step="100"
                    value="${escAttr(String(data.precio_oferta || ''))}" placeholder="Dejar vacio si no aplica">
            </div>
            <div class="col-4">
                <label class="form-label fw-semibold">Stock</label>
                <input id="swal-prod-stock" class="form-control" type="number" min="0"
                    value="${escAttr(String(data.stock ?? '0'))}" placeholder="0">
            </div>
            <div class="col-4">
                <label class="form-label fw-semibold">Stock minimo</label>
                <input id="swal-prod-stock-min" class="form-control" type="number" min="0"
                    value="${escAttr(String(data.stock_minimo ?? '5'))}" placeholder="5">
            </div>
            <div class="col-4 d-flex align-items-end pb-1">
                <div class="form-check">
                    <input id="swal-prod-destacado" class="form-check-input" type="checkbox" ${destChecked}>
                    <label class="form-check-label fw-semibold" for="swal-prod-destacado">
                        <i class="bi bi-star-fill text-warning me-1"></i>Destacado
                    </label>
                </div>
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Categoria</label>
                <select id="swal-prod-cat" class="form-select">
                    <option value="">Sin categoria</option>
                    ${catOptions}
                </select>
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Estado</label>
                <select id="swal-prod-estado" class="form-select">
                    <option value="activo"   ${estadoActivo}>Activo</option>
                    <option value="inactivo" ${estadoInactivo}>Inactivo</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Imagen principal</label>
                <input id="swal-file-prod" class="form-control" type="file"
                    accept="image/jpeg,image/png,image/webp,image/gif">
                <img id="previewImgProd" src="" alt="Preview">
            </div>
        </div>
    </div>`;
    }

    /* ══════════════ GALERIA DE PRODUCTO ══════════════ */

    function gestionarGaleria(productoId, productoNombre) {
        Swal.fire({
            title: `<i class="bi bi-images me-2 text-info"></i>Galeria`,
            html: `<p class="text-muted mb-3 fw-semibold">${escHtml(productoNombre)}</p>
               <div id="galeria-container" class="text-center py-3">
                   <div class="spinner-border text-success" role="status"></div>
               </div>`,
            showConfirmButton: false,
            showCloseButton: true,
            width: 720,
            didOpen: () => cargarGaleria(productoId)
        });
    }

    async function cargarGaleria(productoId) {
        const res = await fetch(`${API_IMGS}?action=get&producto_id=${productoId}`);
        const data = await res.json();
        renderGaleria(data.data || [], productoId);
    }

    function renderGaleria(imagenes, productoId) {
        const container = document.getElementById('galeria-container');
        if (!container) return;
        const total = imagenes.length;

        let imgsHtml = '';
        if (total === 0) {
            imgsHtml = '<p class="text-muted text-center mb-3 small">Sin imagenes adicionales aun.</p>';
        } else {
            imgsHtml = '<div class="row g-2 mb-3">';
            imagenes.forEach(img => {
                imgsHtml += `
            <div class="col-4 col-md-3" id="gal-item-${img.id}">
                <div class="position-relative">
                    <img src="${escAttr(img.imagen)}" class="gal-thumb" alt="imagen galeria">
                    <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 px-1 lh-sm"
                        style="font-size:.75rem"
                        onclick="eliminarImagenGaleria(${img.id},${productoId})"
                        title="Eliminar imagen">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>`;
            });
            imgsHtml += '</div>';
        }

        let uploadHtml = '';
        if (total < 8) {
            uploadHtml = `
        <div class="text-start border-top pt-3">
            <p class="text-muted small mb-2">
                <i class="bi bi-info-circle me-1"></i>${total} de 8 imagenes utilizadas
            </p>
            <div class="d-flex gap-2 align-items-center">
                <input type="file" id="gal-file-${productoId}" class="form-control form-control-sm"
                    accept="image/jpeg,image/png,image/webp,image/gif">
                <button id="gal-btn-${productoId}" class="btn btn-success btn-sm text-nowrap"
                    onclick="subirImagenGaleria(${productoId})">
                    <i class="bi bi-cloud-upload me-1"></i>Subir
                </button>
            </div>
        </div>`;
        } else {
            uploadHtml = `<p class="badge bg-warning text-dark mt-2">
            <i class="bi bi-exclamation-triangle me-1"></i>Limite de 8 imagenes alcanzado
        </p>`;
        }

        container.innerHTML = imgsHtml + uploadHtml;
    }

    async function subirImagenGaleria(productoId) {
        const fileInput = document.getElementById(`gal-file-${productoId}`);
        const btn = document.getElementById(`gal-btn-${productoId}`);

        if (!fileInput || !fileInput.files[0]) {
            alert('Selecciona una imagen primero.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Subiendo...';

        const fd = new FormData();
        fd.append('action', 'upload');
        fd.append('producto_id', productoId);
        fd.append('imagen', fileInput.files[0]);

        const res = await fetch(API_IMGS, {
            method: 'POST',
            body: fd
        });
        const data = await res.json();

        if (data.success) {
            await cargarGaleria(productoId);
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cloud-upload me-1"></i>Subir';
            alert('Error: ' + data.message);
        }
    }

    async function eliminarImagenGaleria(imgId, productoId) {
        if (!confirm('¿Eliminar esta imagen? Esta accion no se puede deshacer.')) return;

        const item = document.getElementById(`gal-item-${imgId}`);
        if (item) item.style.opacity = '0.4';

        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', imgId);

        const res = await fetch(API_IMGS, {
            method: 'POST',
            body: fd
        });
        const data = await res.json();

        if (data.success) {
            await cargarGaleria(productoId);
        } else {
            if (item) item.style.opacity = '1';
            alert('Error: ' + data.message);
        }
    }



    /* ══════════════ ATRIBUTOS (COLORES / TALLAS) ══════════════ */

    function gestionarAtributos(productoId, productoNombre) {
        Swal.fire({
            title: `<i class="bi bi-sliders text-secondary me-2"></i>Atributos`,
            html: `<p class="text-muted fw-semibold mb-3">${escHtml(productoNombre)}</p>
               <div id="attrs-container" class="text-center py-3">
                   <div class="spinner-border text-secondary" role="status"></div>
               </div>`,
            showConfirmButton: false,
            showCloseButton: true,
            width: 680,
            didOpen: () => cargarAtributos(productoId)
        });
    }

    async function cargarAtributos(productoId) {
        const res = await fetch(`${API_ATTRS}?action=get&producto_id=${productoId}`);
        const data = await res.json();
        renderAtributos(data.data || [], productoId);
    }

    function renderAtributos(attrs, productoId) {
        const cont = document.getElementById('attrs-container');
        if (!cont) return;
        const colores = attrs.filter(a => a.tipo === 'color');
        const tallas = attrs.filter(a => a.tipo === 'talla');

        const renderBadges = (lista, tipo) => {
            if (lista.length === 0) return `<span class="text-muted small">Sin ${tipo}s registrados</span>`;
            return lista.map(a => {
                const opac = a.disponible == 1 ? '' : 'opacity-50';
                const label = tipo === 'color' ?
                    `<span class="badge me-1 ${opac}" style="background-color:${escAttr(a.valor)};min-width:54px;border:1px solid #dee2e6">&nbsp;${escHtml(a.valor)}&nbsp;</span>` :
                    `<span class="badge bg-secondary me-1 ${opac}">${escHtml(a.valor)}</span>`;
                return `<span class="d-inline-flex align-items-center gap-1 me-1 mb-1">
                ${label}
                <button class="btn btn-link btn-sm p-0 text-danger" style="font-size:.72rem;line-height:1"
                    onclick="eliminarAtributo(${a.id},${productoId})" title="Eliminar">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </span>`;
            }).join('');
        };

        cont.innerHTML = `
    <div class="text-start">
        <div class="mb-3">
            <h6 class="fw-semibold text-secondary mb-2"><i class="bi bi-palette me-1"></i>Colores</h6>
            <div id="attrs-colores" class="mb-1">${renderBadges(colores, 'color')}</div>
        </div>
        <div class="mb-3">
            <h6 class="fw-semibold text-secondary mb-2"><i class="bi bi-rulers me-1"></i>Tallas</h6>
            <div id="attrs-tallas" class="mb-1">${renderBadges(tallas, 'talla')}</div>
        </div>
        <div class="border-top pt-3">
            <h6 class="fw-semibold mb-2"><i class="bi bi-plus-circle me-1"></i>Agregar atributo</h6>
            <div class="d-flex gap-2">
                <select id="attr-tipo" class="form-select form-select-sm" style="max-width:120px">
                    <option value="color">Color</option>
                    <option value="talla">Talla</option>
                </select>
                <input id="attr-valor" class="form-control form-control-sm" type="text"
                    placeholder="Ej: Rojo, XL, 42..."
                    onkeydown="if(event.key==='Enter'){event.preventDefault();agregarAtributo(${productoId});}">
                <button class="btn btn-success btn-sm text-nowrap" onclick="agregarAtributo(${productoId})">
                    <i class="bi bi-plus-lg me-1"></i>Agregar
                </button>
            </div>
            <div id="attr-msg" class="text-danger small mt-1"></div>
        </div>
    </div>`;
    }

    async function agregarAtributo(productoId) {
        const tipo = document.getElementById('attr-tipo').value;
        const valor = document.getElementById('attr-valor').value.trim();
        const msg = document.getElementById('attr-msg');
        if (!valor) {
            msg.textContent = 'Escribe el valor del atributo.';
            return;
        }
        msg.textContent = '';
        const fd = new FormData();
        fd.append('action', 'create');
        fd.append('producto_id', productoId);
        fd.append('tipo', tipo);
        fd.append('valor', valor);
        const res = await fetch(API_ATTRS, {
            method: 'POST',
            body: fd
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('attr-valor').value = '';
            await cargarAtributos(productoId);
        } else {
            msg.textContent = data.message;
        }
    }

    async function eliminarAtributo(attrId, productoId) {
        if (!confirm('¿Eliminar este atributo?')) return;
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', attrId);
        const res = await fetch(API_ATTRS, {
            method: 'POST',
            body: fd
        });
        const data = await res.json();
        if (data.success) await cargarAtributos(productoId);
        else alert('Error: ' + data.message);
    }

    /* ══════════════ STOCK E INVENTARIO ══════════════ */

    function cargarStock() {
        fetch(API_STOCK + '?action=list')
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                if (dtStock) {
                    dtStock.destroy();
                    dtStock = null;
                }

                const data = res.data;
                const sinStock = data.filter(p => parseInt(p.stock) === 0).length;
                const bajo = data.filter(p => parseInt(p.stock) > 0 && parseInt(p.stock) <= parseInt(p.stock_minimo || 5)).length;
                const ok = data.length - sinStock - bajo;

                document.getElementById('stockResumen').innerHTML = `
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm text-center py-3">
                        <div class="fs-1 fw-bold text-success">${ok}</div>
                        <div class="text-muted">Stock OK</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm text-center py-3" style="border-left:4px solid #ffc107!important">
                        <div class="fs-1 fw-bold text-warning">${bajo}</div>
                        <div class="text-muted">Stock Bajo</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm text-center py-3" style="border-left:4px solid #dc3545!important">
                        <div class="fs-1 fw-bold text-danger">${sinStock}</div>
                        <div class="text-muted">Sin Stock</div>
                    </div>
                </div>`;

                let filas = '';
                data.forEach(p => {
                    const stock = parseInt(p.stock);
                    const minimo = parseInt(p.stock_minimo || 5);
                    let etiqueta, cls;
                    if (stock === 0) {
                        etiqueta = 'Sin stock';
                        cls = 'bg-danger';
                    } else if (stock <= minimo) {
                        etiqueta = 'Stock bajo';
                        cls = 'bg-warning text-dark';
                    } else {
                        etiqueta = 'OK';
                        cls = 'bg-success';
                    }

                    filas += `
                <tr>
                    <td class="text-center"><span class="serie-badge">${escHtml(p.numero_serie || '—')}</span></td>
                    <td>${escHtml(p.nombre)}</td>
                    <td>${escHtml(p.categoria || '—')}</td>
                    <td class="text-center fw-bold fs-5 ${stock === 0 ? 'text-danger' : stock <= minimo ? 'text-warning' : 'text-success'}">${stock}</td>
                    <td class="text-center text-muted">${minimo}</td>
                    <td class="text-center"><span class="badge ${cls}">${etiqueta}</span></td>
                    <td class="text-center">
                        <button class="btn btn-primary btn-sm me-1" title="Registrar movimiento"
                            onclick="registrarMovimiento(${p.id},'${escAttr(p.nombre)}',${stock})">
                            <i class="bi bi-arrow-left-right me-1"></i>Movimiento
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" title="Ver historial"
                            onclick="verHistorialStock(${p.id},'${escAttr(p.nombre)}')">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    </td>
                </tr>`;
                });

                document.getElementById('bodyStock').innerHTML = filas || '<tr><td colspan="7" class="text-center text-muted">Sin productos</td></tr>';

                dtStock = $('#tablaStock').DataTable({
                    responsive: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    pagingType: 'simple',
                    columnDefs: [{
                        orderable: false,
                        targets: [6]
                    }],
                    order: [
                        [3, 'asc']
                    ]
                });
            });
    }

    function registrarMovimiento(productoId, productoNombre, stockActual) {
        Swal.fire({
            title: `<i class="bi bi-arrow-left-right text-primary me-2"></i>Registrar Movimiento`,
            html: `
        <p class="fw-semibold text-muted mb-3">${escHtml(productoNombre)}</p>
        <div class="text-start">
            <div class="mb-3 p-2 bg-light rounded text-center">
                <span class="text-muted small">Stock actual:</span>
                <span class="ms-2 fw-bold fs-4">${stockActual}</span>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold">Tipo de movimiento <span class="text-danger">*</span></label>
                <select id="swal-mov-tipo" class="form-select" onchange="
                    const t=this.value;
                    const l=document.getElementById('swal-mov-cant-label');
                    l.textContent=t==='entrada'?'Cantidad a ingresar':t==='salida'?'Cantidad a retirar':'Nuevo stock total';
                ">
                    <option value="entrada">Entrada — suma al stock</option>
                    <option value="salida">Salida — resta del stock</option>
                    <option value="ajuste">Ajuste — establece stock exacto</option>
                </select>
            </div>
            <div class="mb-2">
                <label id="swal-mov-cant-label" class="form-label fw-semibold">Cantidad a ingresar <span class="text-danger">*</span></label>
                <input id="swal-mov-cant" class="form-control" type="number" min="1" value="1">
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold">Motivo / Observacion</label>
                <input id="swal-mov-motivo" class="form-control" type="text"
                    placeholder="Ej: Compra proveedor, Venta, Perdida...">
            </div>
        </div>`,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Registrar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0d6efd',
            focusConfirm: false,
            preConfirm: () => {
                const tipo = document.getElementById('swal-mov-tipo').value;
                const cantidad = parseInt(document.getElementById('swal-mov-cant').value);
                const motivo = document.getElementById('swal-mov-motivo').value.trim();
                if (!cantidad || cantidad <= 0) {
                    Swal.showValidationMessage('La cantidad debe ser mayor a 0');
                    return false;
                }
                const fd = new FormData();
                fd.append('action', 'movimiento');
                fd.append('producto_id', productoId);
                fd.append('tipo', tipo);
                fd.append('cantidad', cantidad);
                fd.append('motivo', motivo);
                return fetch(API_STOCK, {
                    method: 'POST',
                    body: fd
                }).then(r => r.json());
            }
        }).then(result => {
            if (result.isConfirmed && result.value?.success) {
                const d = result.value;
                Swal.fire({
                    icon: 'success',
                    title: 'Movimiento registrado',
                    html: `Stock actualizado: <strong>${d.stock_anterior}</strong> → <strong>${d.stock_nuevo}</strong>`,
                    timer: 2500,
                    showConfirmButton: false
                });
                cargarStock();
                dtProductos = null;
            } else if (result.isConfirmed && result.value) {
                Swal.fire('Error', result.value.message, 'error');
            }
        });
    }

    function verHistorialStock(productoId, productoNombre) {
        Swal.fire({
            title: `<i class="bi bi-clock-history text-secondary me-2"></i>Historial de Stock`,
            html: `<p class="fw-semibold text-muted mb-3">${escHtml(productoNombre)}</p>
               <div id="hist-container" class="text-center py-3">
                   <div class="spinner-border text-secondary" role="status"></div>
               </div>`,
            showConfirmButton: false,
            showCloseButton: true,
            width: 720,
            didOpen: async () => {
                const res = await fetch(`${API_STOCK}?action=movimientos&producto_id=${productoId}`);
                const data = await res.json();
                const cont = document.getElementById('hist-container');
                if (!data.success || !data.data.length) {
                    cont.innerHTML = '<p class="text-muted">Sin movimientos registrados aun.</p>';
                    return;
                }
                const ic = {
                    entrada: '↑',
                    salida: '↓',
                    ajuste: '≡'
                };
                const cls = {
                    entrada: 'bg-success',
                    salida: 'bg-danger',
                    ajuste: 'bg-primary'
                };
                const rows = data.data.map(m => `
                <tr>
                    <td class="text-center">
                        <span class="badge ${cls[m.tipo]}">${ic[m.tipo]} ${m.tipo}</span>
                    </td>
                    <td class="text-center fw-semibold">${m.cantidad}</td>
                    <td class="text-center">${m.stock_anterior} → <strong>${m.stock_nuevo}</strong></td>
                    <td>${escHtml(m.motivo || '—')}</td>
                    <td class="text-center small text-muted">${(m.fecha || '').substring(0,16)}</td>
                </tr>`).join('');
                cont.innerHTML = `
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover text-start">
                    <thead class="table-secondary text-center">
                        <tr><th>Tipo</th><th>Cantidad</th><th>Stock</th><th>Motivo</th><th>Fecha</th></tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
            }
        });
    }

    /* ══════════════ DUPLICAR PRODUCTO ══════════════ */

    function duplicarProducto(id, nombre) {
        Swal.fire({
            title: '¿Duplicar producto?',
            html: `<p>Se creara una copia de <strong>${escHtml(nombre)}</strong><br>con estado <em>inactivo</em> y stock en 0.</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-files me-1"></i>Duplicar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754'
        }).then(result => {
            if (!result.isConfirmed) return;
            const fd = new FormData();
            fd.append('action', 'duplicate');
            fd.append('id', id);
            fetch(API_PROD, {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Duplicado',
                            text: `N.° Serie: ${res.numero_serie}`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        cargarProductos();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
        });
    }

    /* ══════════════ TOGGLE DESTACADO ══════════════ */

    function toggleDestacado(id, nuevoValor) {
        const fd = new FormData();
        fd.append('action', 'toggle_destacado');
        fd.append('id', id);
        fd.append('destacado', nuevoValor);
        fetch(API_PROD, {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(() => {
                const filtros = {};
                const cat = document.getElementById('filtroCat')?.value;
                const est = document.getElementById('filtroEst')?.value;
                const desc = document.getElementById('filtroDesc')?.value;
                if (cat) filtros.categoria = cat;
                if (est) filtros.estado = est;
                if (desc) filtros.destacado = desc;
                cargarProductos(filtros);
            });
    }

    /* ══════════════ FILTROS ══════════════ */

    function filtrarProductos() {
        const filtros = {};
        const cat = document.getElementById('filtroCat')?.value;
        const est = document.getElementById('filtroEst')?.value;
        const desc = document.getElementById('filtroDesc')?.value;
        if (cat) filtros.categoria = cat;
        if (est) filtros.estado = est;
        if (desc) filtros.destacado = desc;
        cargarProductos(filtros);
    }

    function limpiarFiltros() {
        ['filtroCat', 'filtroEst', 'filtroDesc'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        cargarProductos();
    }

    /* ── Utilidades ──────────────────────────────────────────────────── */

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function escAttr(str) {
        if (!str) return '';
        return String(str).replace(/'/g, '&#39;').replace(/"/g, '&quot;');
    }

    function previewFile(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        if (!file) {
            preview.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    /* ── Carga inicial al abrir cada tab ─────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function() {
        cargarCategorias();

        document.getElementById('tab-prod-btn').addEventListener('shown.bs.tab', function() {
            if (!dtProductos) cargarProductos();
        });

        document.getElementById('tab-stock-btn').addEventListener('shown.bs.tab', function() {
            if (!dtStock) cargarStock();
            else {
                dtStock.destroy();
                dtStock = null;
                cargarStock();
            }
        });
    });
</script>