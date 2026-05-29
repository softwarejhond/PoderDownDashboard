<?php
// components/products/products_categories.php
// Componente incluido en listProducts.php — NO usar etiquetas html/body/head
?>

<div class="container-fluid px-0">

    <!-- ============================================================
         NAVTABS
    ============================================================ -->
    <ul class="nav nav-tabs border-bottom" id="prodCatTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold px-4" id="btn-tab-cat"
                    data-bs-toggle="tab" data-bs-target="#tab-categorias" type="button" role="tab">
                <i class="bi bi-tags-fill me-1"></i> Categorías
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold px-4" id="btn-tab-prod"
                    data-bs-toggle="tab" data-bs-target="#tab-productos" type="button" role="tab">
                <i class="bi bi-box-seam-fill me-1"></i> Productos
            </button>
        </li>
    </ul>

    <div class="tab-content pt-3" id="prodCatTabsContent">

        <!-- ========================================================
             TAB — CATEGORÍAS
        ======================================================== -->
        <div class="tab-pane fade show active" id="tab-categorias" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Gestione las categorías del catálogo de productos.
                </span>
                <button class="btn btn-primary btn-sm" onclick="modalAgregarCategoria()">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Categoría
                </button>
            </div>

            <div class="table-responsive">
                <table id="tablaCategorias" class="table table-bordered table-hover table-sm" style="width:100%">
                    <thead class="table-dark text-center">
                        <tr>
                            <th style="width:45px">#</th>
                            <th class="text-start">Nombre</th>
                            <th style="width:70px">Ícono</th>
                            <th class="text-start">Descripción</th>
                            <th style="width:90px">Productos</th>
                            <th style="width:65px">Orden</th>
                            <th style="width:90px">Estado</th>
                            <th style="width:90px">Destacado</th>
                            <th style="width:100px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div><!-- /tab-categorias -->

        <!-- ========================================================
             TAB — PRODUCTOS
        ======================================================== -->
        <div class="tab-pane fade" id="tab-productos" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Gestione el catálogo de productos de la tienda.
                </span>
                <button class="btn btn-success btn-sm" onclick="modalAgregarProducto()">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
                </button>
            </div>

            <div class="table-responsive">
                <table id="tablaProductos" class="table table-bordered table-hover table-sm" style="width:100%">
                    <thead class="table-dark text-center">
                        <tr>
                            <th style="width:90px">SKU</th>
                            <th class="text-start">Nombre</th>
                            <th style="width:130px">Categoría</th>
                            <th style="width:110px">Precio</th>
                            <th style="width:140px">P. Comparación</th>
                            <th style="width:90px">Stock</th>
                            <th style="width:90px">Estado</th>
                            <th style="width:90px">Destacado</th>
                            <th style="width:100px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div><!-- /tab-productos -->

    </div><!-- /tab-content -->
</div><!-- /container-fluid -->

<!-- ================================================================
     JAVASCRIPT
================================================================ -->
<script>
/* ----------------------------------------------------------------
   UTILIDADES
---------------------------------------------------------------- */
function escHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function formatPrice(val) {
    if (!val || parseFloat(val) === 0) return '—';
    return '$ ' + parseFloat(val).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/* ================================================================
   CATEGORÍAS
================================================================ */
let dtCategorias    = null;
let categoriasLista = [];

/* ---- Cargar tabla de categorías ---- */
function cargarCategorias() {
    $.ajax({
        url: 'components/products/api_categories.php',
        data: { action: 'get' },
        method: 'GET',
        dataType: 'json',
        success: function (resp) {
            if (!resp.success) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar las categorías.' });
                return;
            }
            categoriasLista = resp.data;

            if (dtCategorias) {
                dtCategorias.clear().destroy();
                $('#tablaCategorias tbody').empty();
            }

            dtCategorias = $('#tablaCategorias').DataTable({
                data: categoriasLista,
                pageLength: 10,
                language: { url: 'controller/datatable_esp.json' },
                columns: [
                    { data: 'id', className: 'text-center' },
                    {
                        data: 'name',
                        render: d => `<strong>${escHtml(d)}</strong>`
                    },
                    {
                        data: 'icon',
                        className: 'text-center',
                        render: d => d ? `<i class="${escHtml(d)} fs-5" title="${escHtml(d)}"></i>` : '<span class="text-muted">—</span>'
                    },
                    {
                        data: 'description',
                        render: d => d
                            ? `<small class="text-muted">${escHtml(d).substring(0, 70)}${d.length > 70 ? '…' : ''}</small>`
                            : '<span class="text-muted">—</span>'
                    },
                    {
                        data: 'total_products',
                        className: 'text-center',
                        render: d => `<span class="badge bg-secondary">${d}</span>`
                    },
                    { data: 'sort_order', className: 'text-center' },
                    {
                        data: 'is_active',
                        className: 'text-center',
                        render: d => d == 1
                            ? '<span class="badge bg-success">Activo</span>'
                            : '<span class="badge bg-danger">Inactivo</span>'
                    },
                    {
                        data: 'is_featured',
                        className: 'text-center',
                        render: d => d == 1
                            ? '<i class="bi bi-star-fill text-warning fs-5"></i>'
                            : '<i class="bi bi-star text-muted fs-5"></i>'
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: (d, t, row) =>
                            `<button class="btn btn-outline-primary btn-sm me-1" title="Editar" onclick="modalEditarCategoria(${row.id})">
                                <i class="bi bi-pencil-fill"></i>
                             </button>
                             <button class="btn btn-outline-danger btn-sm" title="Eliminar" onclick="confirmarEliminarCategoria(${row.id},'${escHtml(row.name)}')">
                                <i class="bi bi-trash-fill"></i>
                             </button>`
                    }
                ]
            });
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Error de red', text: 'No fue posible conectar con el servidor.' });
        }
    });
}

/* ---- HTML del formulario de categoría ---- */
function htmlFormCategoria(data) {
    const d = data || {};
    return `
        <div class="text-start">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input id="catName" class="form-control" placeholder="Ej: Electrónica"
                       value="${escHtml(d.name || '')}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea id="catDesc" class="form-control" rows="2"
                          placeholder="Descripción breve de la categoría">${escHtml(d.description || '')}</textarea>
            </div>
            <div class="row g-2">
                <div class="col-md-7 mb-2">
                    <label class="form-label fw-semibold">
                        Ícono <small class="text-muted fw-normal">(clase Bootstrap Icons)</small>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" id="catIconPreview">
                            <i id="catIconPrev" class="${escHtml(d.icon || 'bi bi-tag')} fs-5"></i>
                        </span>
                        <input id="catIcon" class="form-control" placeholder="Ej: bi bi-laptop"
                               value="${escHtml(d.icon || '')}"
                               oninput="document.getElementById('catIconPrev').className = this.value || 'bi bi-tag'">
                    </div>
                </div>
                <div class="col-md-5 mb-2">
                    <label class="form-label fw-semibold">Orden de visualización</label>
                    <input id="catOrder" type="number" class="form-control"
                           value="${d.sort_order !== undefined ? d.sort_order : 0}" min="0">
                </div>
            </div>
            <div class="d-flex gap-4 mt-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="catActive"
                           ${(d.is_active === undefined || d.is_active == 1) ? 'checked' : ''}>
                    <label class="form-check-label" for="catActive">Activo</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="catFeatured"
                           ${d.is_featured == 1 ? 'checked' : ''}>
                    <label class="form-check-label" for="catFeatured">Destacado</label>
                </div>
            </div>
        </div>`;
}

/* ---- Leer valores del formulario de categoría ---- */
function getCatFormValues() {
    const name = document.getElementById('catName').value.trim();
    if (!name) {
        Swal.showValidationMessage('El nombre de la categoría es obligatorio');
        return false;
    }
    return {
        name,
        description:  document.getElementById('catDesc').value.trim(),
        icon:         document.getElementById('catIcon').value.trim(),
        sort_order:   document.getElementById('catOrder').value || 0,
        is_active:    document.getElementById('catActive').checked   ? 1 : 0,
        is_featured:  document.getElementById('catFeatured').checked ? 1 : 0,
    };
}

/* ---- Modal: Agregar categoría ---- */
function modalAgregarCategoria() {
    Swal.fire({
        title: '<i class="bi bi-tag-fill text-primary me-2"></i>Nueva Categoría',
        width: 580,
        html: htmlFormCategoria(),
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-save me-1"></i> Guardar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#0d6efd',
        preConfirm: getCatFormValues
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_categories.php', { action: 'create', ...result.value }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: '¡Guardado!', text: resp.message, timer: 1800, showConfirmButton: false });
                    cargarCategorias();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
}

/* ---- Modal: Editar categoría ---- */
function modalEditarCategoria(id) {
    const cat = categoriasLista.find(c => c.id == id);
    if (!cat) return;

    Swal.fire({
        title: '<i class="bi bi-pencil-fill text-warning me-2"></i>Editar Categoría',
        width: 580,
        html: htmlFormCategoria(cat),
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-save me-1"></i> Actualizar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#fd7e14',
        preConfirm: getCatFormValues
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_categories.php', { action: 'update', id, ...result.value }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: '¡Actualizado!', text: resp.message, timer: 1800, showConfirmButton: false });
                    cargarCategorias();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
}

/* ---- Confirmar eliminación de categoría ---- */
function confirmarEliminarCategoria(id, nombre) {
    Swal.fire({
        title: '¿Eliminar categoría?',
        html: `<p>¿Seguro que deseas eliminar la categoría <strong>${escHtml(nombre)}</strong>?</p>
               <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Esta acción no se puede deshacer.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Eliminar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#dc3545',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_categories.php', { action: 'delete', id }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Eliminada', text: resp.message, timer: 1800, showConfirmButton: false });
                    cargarCategorias();
                } else {
                    Swal.fire({ icon: 'error', title: 'No se puede eliminar', text: resp.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
}

/* ================================================================
   PRODUCTOS
================================================================ */
let dtProductos  = null;
let productosData = [];

/* ---- Cargar tabla de productos ---- */
function cargarProductos() {
    $.ajax({
        url: 'components/products/api_products.php',
        data: { action: 'get' },
        method: 'GET',
        dataType: 'json',
        success: function (resp) {
            if (!resp.success) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los productos.' });
                return;
            }
            productosData = resp.data;

            if (dtProductos) {
                dtProductos.clear().destroy();
                $('#tablaProductos tbody').empty();
            }

            dtProductos = $('#tablaProductos').DataTable({
                data: productosData,
                pageLength: 10,
                language: { url: 'controller/datatable_esp.json' },
                columns: [
                    {
                        data: 'sku',
                        render: d => `<code class="text-primary small">${escHtml(d)}</code>`
                    },
                    {
                        data: 'name',
                        render: (d, t, row) => {
                            const star = row.is_featured == 1 ? ' <i class="bi bi-star-fill text-warning" title="Destacado"></i>' : '';
                            const digital = row.is_digital == 1 ? ' <span class="badge bg-light text-dark border small">Digital</span>' : '';
                            return `<strong>${escHtml(d)}</strong>${star}${digital}`;
                        }
                    },
                    {
                        data: 'category_name',
                        className: 'text-center',
                        render: d => d
                            ? `<span class="badge bg-info text-dark">${escHtml(d)}</span>`
                            : '<span class="text-muted small">—</span>'
                    },
                    {
                        data: 'price',
                        className: 'text-end',
                        render: d => `<span class="text-success fw-semibold">${formatPrice(d)}</span>`
                    },
                    {
                        data: 'compare_price',
                        className: 'text-end',
                        render: (d, t, row) => {
                            if (!d || parseFloat(d) === 0) return '<span class="text-muted">—</span>';
                            const base = parseFloat(row.price);
                            const comp = parseFloat(d);
                            const pct  = base > 0 ? Math.round((1 - base / comp) * 100) : 0;
                            return `<span class="text-decoration-line-through text-muted">${formatPrice(d)}</span>
                                    <span class="badge bg-danger ms-1">-${pct}%</span>`;
                        }
                    },
                    {
                        data: 'stock',
                        className: 'text-center',
                        render: (d, t, row) => {
                            const low = parseInt(row.low_stock_threshold) || 5;
                            const qty = parseInt(d);
                            if (qty <= 0)   return `<span class="badge bg-danger">Sin stock</span>`;
                            if (qty <= low) return `<span class="badge bg-warning text-dark">${qty} <small>(bajo)</small></span>`;
                            return `<span class="badge bg-success">${qty}</span>`;
                        }
                    },
                    {
                        data: 'is_active',
                        className: 'text-center',
                        render: d => d == 1
                            ? '<span class="badge bg-success">Activo</span>'
                            : '<span class="badge bg-secondary">Inactivo</span>'
                    },
                    {
                        data: 'is_featured',
                        className: 'text-center',
                        render: d => d == 1
                            ? '<i class="bi bi-star-fill text-warning fs-5"></i>'
                            : '<i class="bi bi-star text-muted fs-5"></i>'
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: (d, t, row) =>
                            `<button class="btn btn-outline-warning btn-sm me-1" title="Editar" onclick="modalEditarProducto(${row.id})">
                                <i class="bi bi-pencil-fill"></i>
                             </button>
                             <button class="btn btn-outline-danger btn-sm" title="Eliminar" onclick="confirmarEliminarProducto(${row.id},'${escHtml(row.name)}')">
                                <i class="bi bi-trash-fill"></i>
                             </button>`
                    }
                ]
            });
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Error de red', text: 'No fue posible conectar con el servidor.' });
        }
    });
}

/* ---- HTML del formulario de producto ---- */
function htmlFormProducto(data) {
    const d = data || {};
    const catsOpts = categoriasLista
        .map(c => `<option value="${c.id}" ${d.category_id == c.id ? 'selected' : ''}>${escHtml(c.name)}</option>`)
        .join('');

    return `
        <div class="text-start" style="max-height:65vh;overflow-y:auto;padding-right:4px;">

            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">SKU <span class="text-danger">*</span></label>
                    <input id="prodSku" class="form-control form-control-sm"
                           placeholder="Ej: PROD-001" value="${escHtml(d.sku || '')}">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input id="prodName" class="form-control form-control-sm"
                           placeholder="Nombre del producto" value="${escHtml(d.name || '')}">
                </div>
            </div>

            <div class="mt-2">
                <label class="form-label fw-semibold">Descripción corta</label>
                <input id="prodShortDesc" class="form-control form-control-sm"
                       placeholder="Resumen breve (máx. 500 caracteres)"
                       maxlength="500" value="${escHtml(d.short_description || '')}">
            </div>

            <div class="mt-2">
                <label class="form-label fw-semibold">Descripción completa</label>
                <textarea id="prodDesc" class="form-control form-control-sm" rows="3"
                          placeholder="Descripción detallada del producto">${escHtml(d.description || '')}</textarea>
            </div>

            <div class="row g-2 mt-1">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Categoría</label>
                    <select id="prodCat" class="form-select form-select-sm">
                        <option value="">— Sin categoría —</option>
                        ${catsOpts}
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tags <small class="text-muted fw-normal">(separados por coma)</small></label>
                    <input id="prodTags" class="form-control form-control-sm"
                           placeholder="Ej: nuevo, oferta, verano" value="${escHtml(d.tags || '')}">
                </div>
            </div>

            <hr class="my-2">
            <p class="fw-semibold mb-1 text-muted small text-uppercase">Precios</p>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Precio <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input id="prodPrice" type="number" step="0.01" min="0"
                               class="form-control" placeholder="0.00"
                               value="${d.price || ''}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Precio comparación <small class="text-muted">(tachado)</small></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input id="prodComparePrice" type="number" step="0.01" min="0"
                               class="form-control" placeholder="0.00"
                               value="${d.compare_price || ''}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Costo interno</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input id="prodCostPrice" type="number" step="0.01" min="0"
                               class="form-control" placeholder="0.00"
                               value="${d.cost_price || ''}">
                    </div>
                </div>
            </div>

            <hr class="my-2">
            <p class="fw-semibold mb-1 text-muted small text-uppercase">Inventario y estado</p>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                    <input id="prodStock" type="number" min="0"
                           class="form-control form-control-sm" placeholder="0"
                           value="${d.stock !== undefined ? d.stock : 0}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Alerta stock bajo</label>
                    <input id="prodLowStock" type="number" min="0"
                           class="form-control form-control-sm" placeholder="Ej: 5"
                           value="${d.low_stock_threshold || ''}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Estado</label>
                    <select id="prodActive" class="form-select form-select-sm">
                        <option value="1" ${(d.is_active === undefined || d.is_active == 1) ? 'selected' : ''}>Activo</option>
                        <option value="0" ${d.is_active == 0 ? 'selected' : ''}>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-4 mt-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="prodFeatured"
                           ${d.is_featured == 1 ? 'checked' : ''}>
                    <label class="form-check-label" for="prodFeatured">Destacado</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="prodDigital"
                           ${d.is_digital == 1 ? 'checked' : ''}>
                    <label class="form-check-label" for="prodDigital">Producto digital</label>
                </div>
            </div>
        </div>`;
}

/* ---- Leer valores del formulario de producto ---- */
function getProdFormValues() {
    const sku   = document.getElementById('prodSku').value.trim();
    const name  = document.getElementById('prodName').value.trim();
    const price = document.getElementById('prodPrice').value;
    const stock = document.getElementById('prodStock').value;

    if (!sku)  { Swal.showValidationMessage('El SKU es obligatorio'); return false; }
    if (!name) { Swal.showValidationMessage('El nombre del producto es obligatorio'); return false; }
    if (price === '' || isNaN(parseFloat(price)) || parseFloat(price) < 0) {
        Swal.showValidationMessage('Ingresa un precio válido (≥ 0)'); return false;
    }
    if (stock === '' || isNaN(parseInt(stock)) || parseInt(stock) < 0) {
        Swal.showValidationMessage('Ingresa un stock válido (≥ 0)'); return false;
    }

    return {
        sku,
        name,
        short_description:   document.getElementById('prodShortDesc').value.trim(),
        description:         document.getElementById('prodDesc').value.trim(),
        category_id:         document.getElementById('prodCat').value || '',
        tags:                document.getElementById('prodTags').value.trim(),
        price,
        compare_price:       document.getElementById('prodComparePrice').value || '',
        cost_price:          document.getElementById('prodCostPrice').value || '',
        stock,
        low_stock_threshold: document.getElementById('prodLowStock').value || '',
        is_active:           document.getElementById('prodActive').value,
        is_featured:         document.getElementById('prodFeatured').checked ? 1 : 0,
        is_digital:          document.getElementById('prodDigital').checked  ? 1 : 0,
    };
}

/* ---- Modal: Agregar producto ---- */
function modalAgregarProducto() {
    Swal.fire({
        title: '<i class="bi bi-box-seam-fill text-success me-2"></i>Nuevo Producto',
        width: 720,
        html: htmlFormProducto(),
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-save me-1"></i> Guardar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#198754',
        preConfirm: getProdFormValues
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_products.php', { action: 'create', ...result.value }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: '¡Guardado!', text: resp.message, timer: 1800, showConfirmButton: false });
                    cargarProductos();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
}

/* ---- Modal: Editar producto ---- */
function modalEditarProducto(id) {
    const prod = productosData.find(p => p.id == id);
    if (!prod) return;

    Swal.fire({
        title: '<i class="bi bi-pencil-fill text-warning me-2"></i>Editar Producto',
        width: 720,
        html: htmlFormProducto(prod),
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-save me-1"></i> Actualizar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#fd7e14',
        preConfirm: getProdFormValues
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_products.php', { action: 'update', id, ...result.value }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: '¡Actualizado!', text: resp.message, timer: 1800, showConfirmButton: false });
                    cargarProductos();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
}

/* ---- Confirmar eliminación de producto ---- */
function confirmarEliminarProducto(id, nombre) {
    Swal.fire({
        title: '¿Eliminar producto?',
        html: `<p>¿Seguro que deseas eliminar el producto <strong>${escHtml(nombre)}</strong>?</p>
               <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Esta acción no se puede deshacer.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Eliminar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#dc3545',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_products.php', { action: 'delete', id }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Eliminado', text: resp.message, timer: 1800, showConfirmButton: false });
                    cargarProductos();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
}

/* ================================================================
   INICIALIZACIÓN
================================================================ */
$(document).ready(function () {
    // Cargar categorías al inicio (tab activo)
    cargarCategorias();

    // Cargar productos cuando se activa su tab por primera vez
    $('#btn-tab-prod').on('shown.bs.tab', function () {
        if (!dtProductos) {
            cargarProductos();
        }
    });
});
</script>
