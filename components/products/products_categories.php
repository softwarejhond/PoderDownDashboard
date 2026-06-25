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
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold px-4" id="btn-tab-attrs"
                    data-bs-toggle="tab" data-bs-target="#tab-atributos" type="button" role="tab">
                <i class="bi bi-palette-fill me-1"></i> Atributos
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
                <button class="btn bg-indigo-dark text-white btn-sm" onclick="modalAgregarCategoria()">
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
                <button class="btn bg-magenta-dark text-white btn-sm" onclick="modalAgregarProducto()">
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
                            <th class="text-start" style="width:110px">Precio</th>
                            <th class="text-start" style="width:140px">P. Comparación</th>
                            <th class="text-start" style="width:90px">Stock</th>
                            <th class="text-start" style="width:90px">Estado</th>
                            <th class="text-start" style="width:90px">Destacado</th>
                            <th style="width:80px">Fotos</th>
                            <th class="text-start" style="width:100px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div><!-- /tab-productos -->

        <!-- ========================================================
             TAB — ATRIBUTOS
        ======================================================== -->
        <div class="tab-pane fade" id="tab-atributos" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Gestione los atributos de productos (tallas, colores, materiales, etc.).
                </span>
                <button class="btn bg-indigo-dark text-white btn-sm" onclick="modalAgregarAtributo()">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Atributo
                </button>
            </div>

            <div class="table-responsive">
                <table id="tablaAtributos" class="table table-bordered table-hover table-sm" style="width:100%">
                    <thead class="table-dark text-center">
                        <tr>
                            <th style="width:45px">#</th>
                            <th class="text-start">Nombre</th>
                            <th style="width:85px">Tipo</th>
                            <th style="width:85px"># Valores</th>
                            <th style="width:70px">Orden</th>
                            <th style="width:120px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div><!-- /tab-atributos -->

    </div><!-- /tab-content -->
</div><!-- /container-fluid -->

<!-- ================================================================
     ESTILOS PERSONALIZADOS
================================================================ -->
<style>
/* Navtabs - Botones inactivos */
#prodCatTabs .nav-link {
    color: #000 !important;
    transition: color 0.3s ease;
}

/* Navtabs - Botones activos */
#prodCatTabs .nav-link.active {
    color: #30336b !important;
    border-bottom-color: #30336b !important;
}

.prod-price-label {
    min-height: 48px;
    display: flex;
    align-items: flex-end;
}

.prod-price-label-stack {
    display: inline-flex;
    flex-direction: column;
    line-height: 1.1;
}

.prod-price-label-stack small {
    margin-top: 2px;
}
</style>

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
                            `<button class="btn bg-indigo-dark text-white btn-sm me-1" title="Editar" onclick="modalEditarCategoria(${row.id})">
                                <i class="bi bi-pencil-fill"></i>
                             </button>
                             <button class="btn btn-danger text-white btn-sm" title="Eliminar" onclick="confirmarEliminarCategoria(${row.id},'${escHtml(row.name)}')">
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
                        render: d => `<code class="text-teal-dark text-center d-block">${escHtml(d)}</code>`
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
                            ? `<span class="badge bg-purple-light text-dark">${escHtml(d)}</span>`
                            : '<span class="text-muted small">—</span>'
                    },
                    {
                        data: 'price',
                        className: 'text-center',
                        render: d => `<span class="text-success fw-semibold">${formatPrice(d)}</span>`
                    },
                    {
                        data: 'compare_price',
                        className: 'text-center',
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
                        render: (d, t, row) => {
                            const cnt   = row.image_count || 0;
                            const thumb = row.primary_image
                                ? `<img src="${row.primary_image}" style="width:26px;height:26px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6" alt="">`
                                : '<i class="bi bi-image text-muted"></i>';
                            return `<a href="fotos_producto.php?id=${row.id}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" title="Ver/subir imágenes">
                                        ${thumb}
                                        <span class="badge bg-secondary">${cnt}</span>
                                    </a>`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: (d, t, row) =>
                            `<button class="btn bg-magenta-dark text-white btn-sm me-1" title="Editar" onclick="modalEditarProducto(${row.id})">
                                <i class="bi bi-pencil-fill"></i>
                             </button>
                             <button class="btn btn-danger text-white btn-sm" title="Eliminar" onclick="confirmarEliminarProducto(${row.id},'${escHtml(row.name)}')">
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
function htmlFormProducto(data, isNew = false) {
    const d = data || {};
    const catsOpts = categoriasLista
        .map(c => `<option value="${c.id}" ${d.category_id == c.id ? 'selected' : ''}>${escHtml(c.name)}</option>`)
        .join('');

    return `
        <div class="text-start" style="max-height:65vh;overflow-y:auto;padding-right:4px;">

            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">SKU <span class="text-danger">*</span>
                        ${isNew ? '<span class="text-muted fw-normal small ms-1">(auto-generado)</span>' : ''}
                    </label>
                    <input id="prodSku" class="form-control form-control-sm${isNew ? ' bg-light text-muted' : ''}"
                           placeholder="Ej: PROD-001" value="${escHtml(d.sku || '')}"
                           ${isNew ? 'readonly' : ''}>
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
                    <label class="form-label fw-semibold prod-price-label">Precio <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input id="prodPrice" type="number" step="0.01" min="0"
                               class="form-control" placeholder="0.00"
                               value="${d.price || ''}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold prod-price-label">
                        <span class="prod-price-label-stack">
                            <span>Precio comparación</span>
                            <small class="text-muted">(tachado)</small>
                        </span>
                    </label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input id="prodComparePrice" type="number" step="0.01" min="0"
                               class="form-control" placeholder="0.00"
                               value="${d.compare_price || ''}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold prod-price-label">Costo interno</label>
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
                    <label class="form-label fw-semibold">
                        Stock <span class="text-danger">*</span>
                        <small id="stockManagedNote" class="text-warning d-none" style="font-size:0.7rem;">
                            <i class="bi bi-exclamation-triangle-fill ms-1"></i> gestionado por variantes
                        </small>
                    </label>
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
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="prodHasVariants"
                           ${d.has_variants == 1 ? 'checked' : ''}
                           onchange="toggleVariantSection()">
                    <label class="form-check-label" for="prodHasVariants">Tiene variantes</label>
                </div>
            </div>

            <div id="variantSection" class="mt-3" style="display: ${d.has_variants == 1 ? 'block' : 'none'};">
                <hr class="my-2">
                <p class="fw-semibold mb-2 text-muted small text-uppercase">
                    <i class="bi bi-diagram-3 me-1"></i> Variantes del producto
                    <span class="text-warning small fw-normal" style="font-size:0.65rem;">
                        <i class="bi bi-info-circle ms-1"></i> El stock y precio del producto se sincronizan desde las variantes activas
                    </span>
                </p>

                <div class="mb-2">
                    <label class="form-label fw-semibold small">Atributos a combinar</label>
                    <p class="text-muted mb-2" style="font-size:0.7rem;">
                        <i class="bi bi-hand-index-thumb me-1"></i> Marque los atributos que aplican e ingrese los valores manualmente (separados por coma).
                    </p>
                    <div id="attrCheckboxes" style="max-height:220px;overflow-y:auto;"></div>
                    <div class="mt-2 text-end">
                        <button type="button" id="btnGenerateVariants" class="btn btn-outline-success btn-sm"
                                onclick="generarVariantes()">
                            <i class="bi bi-magic me-1"></i> Generar combinaciones
                        </button>
                    </div>
                </div>

                <div id="variantsPreview" class="table-responsive" style="max-height:300px;overflow-y:auto;">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light small text-center">
                            <tr>
                                <th class="text-start">Combinación</th>
                                <th style="width:130px">SKU</th>
                                <th style="width:90px">Precio $</th>
                                <th style="width:70px">Stock</th>
                                <th style="width:60px">Activo</th>
                                <th style="width:45px"></th>
                            </tr>
                        </thead>
                        <tbody id="variantsTableBody">
                            <tr id="variantsEmptyRow">
                                <td colspan="6" class="text-center text-muted small py-3">
                                    <i class="bi bi-info-circle me-1"></i>Seleccione atributos y presione "Generar combinaciones"
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" id="variantsJson" value="">
            </div>
        </div>`;
}

/* ---- Leer valores del formulario de producto ---- */
function getProdFormValues() {
    const sku         = document.getElementById('prodSku').value.trim();
    const name        = document.getElementById('prodName').value.trim();
    const price       = document.getElementById('prodPrice').value;
    const stock       = document.getElementById('prodStock').value;
    const hasVariants = document.getElementById('prodHasVariants').checked;

    if (!sku)  { Swal.showValidationMessage('El SKU es obligatorio'); return false; }
    if (!name) { Swal.showValidationMessage('El nombre del producto es obligatorio'); return false; }
    if (price === '' || isNaN(parseFloat(price)) || parseFloat(price) < 0) {
        Swal.showValidationMessage('Ingresa un precio válido (≥ 0)'); return false;
    }
    if (!hasVariants && (stock === '' || isNaN(parseInt(stock)) || parseInt(stock) < 0)) {
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
    $.get('components/products/api_products.php', { action: 'generate_sku' }, null, 'json')
        .done(function (resp) {
            if (!resp.success) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo generar el SKU. Intenta de nuevo.' });
                return;
            }
            Swal.fire({
                title: '<i class="bi bi-box-seam-fill text-success me-2"></i>Nuevo Producto',
                width: 820,
                html: htmlFormProducto({ sku: resp.sku }, true),
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
                        const newProductId = resp.product_id;
                        editingProductId = newProductId;

                        const hasVariants = document.getElementById('prodHasVariants').checked;
                        if (hasVariants) {
                            const checkedCbs = [...document.querySelectorAll('.attr-check:checked')];
                            const attributesData = [];
                            checkedCbs.forEach(cb => {
                                const attrId = cb.value;
                                const textEl = document.getElementById('attrValuesText_' + attrId);
                                const raw = textEl ? textEl.value.trim() : '';
                                if (raw) {
                                    const values = raw.split(',').map(v => v.trim()).filter(v => v !== '');
                                    if (values.length > 0) {
                                        attributesData.push({ attribute_id: parseInt(attrId), values });
                                    }
                                }
                            });
                            if (attributesData.length > 0) {
                                $.post('components/products/api_product_variants.php', {
                                    action: 'generate',
                                    product_id: newProductId,
                                    attributes_data: JSON.stringify(attributesData)
                                }, null, 'json').always(() => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Producto creado!',
                                        html: `<p>${resp.message}</p>
                                               <p class="mt-2 mb-0 text-muted">¿Deseas agregar imágenes al producto ahora?</p>`,
                                        showCancelButton: true,
                                        confirmButtonText: '<i class="bi bi-images me-1"></i> Agregar fotos',
                                        cancelButtonText:  '<i class="bi bi-clock me-1"></i> Más tarde',
                                        confirmButtonColor: '#198754',
                                        cancelButtonColor:  '#6c757d',
                                    }).then(choice => {
                                        if (choice.isConfirmed) {
                                            window.location.href = 'fotos_producto.php?id=' + newProductId;
                                        } else {
                                            cargarProductos();
                                        }
                                    });
                                });
                                return;
                            }
                        }

                        Swal.fire({
                            icon: 'success',
                            title: '¡Producto creado!',
                            html: `<p>${resp.message}</p>
                                   <p class="mt-2 mb-0 text-muted">¿Deseas agregar imágenes al producto ahora?</p>`,
                            showCancelButton: true,
                            confirmButtonText: '<i class="bi bi-images me-1"></i> Agregar fotos',
                            cancelButtonText:  '<i class="bi bi-clock me-1"></i> Más tarde',
                            confirmButtonColor: '#198754',
                            cancelButtonColor:  '#6c757d',
                        }).then(choice => {
                            if (choice.isConfirmed) {
                                window.location.href = 'fotos_producto.php?id=' + newProductId;
                            } else {
                                cargarProductos();
                            }
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                    }
                })
                .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
        })
        .fail(function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo generar el SKU. Intenta de nuevo.' });
        });
}

/* ---- Modal: Editar producto ---- */
function modalEditarProducto(id) {
    const prod = productosData.find(p => p.id == id);
    if (!prod) return;
    editingProductId = id;

    Swal.fire({
        title: '<i class="bi bi-pencil-fill text-warning me-2"></i>Editar Producto',
        width: 820,
        html: htmlFormProducto(prod),
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-save me-1"></i> Actualizar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#fd7e14',
        didOpen: () => {
            toggleVariantSection();
            if (prod.has_variants == 1) {
                setTimeout(() => {
                    cargarAttrCheckboxes();
                    cargarVariantesEnFormulario();
                }, 200);
            }
        },
        preConfirm: getProdFormValues
    }).then(result => {
        if (!result.isConfirmed) return;

        guardarCambiosVariantes().then(() => {
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
   ATRIBUTOS
================================================================ */
let dtAtributos       = null;
let atributosLista    = [];
let atributosConValores = [];

function cargarAtributos() {
    $.ajax({
        url: 'components/products/api_product_attributes.php',
        data: { action: 'get' },
        method: 'GET',
        dataType: 'json',
        success: function (resp) {
            if (!resp.success) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los atributos.' });
                return;
            }
            atributosLista = resp.data;

            if (dtAtributos) {
                dtAtributos.clear().destroy();
                $('#tablaAtributos tbody').empty();
            }

            dtAtributos = $('#tablaAtributos').DataTable({
                data: atributosLista,
                pageLength: 10,
                language: { url: 'controller/datatable_esp.json' },
                columns: [
                    { data: 'id', className: 'text-center' },
                    {
                        data: 'name',
                        render: d => `<strong>${escHtml(d)}</strong>`
                    },
                    {
                        data: 'type',
                        className: 'text-center',
                        render: d => {
                            const map = { select: 'Select', color: 'Color', text: 'Texto' };
                            const cls = { select: 'secondary', color: 'primary', text: 'dark' };
                            return `<span class="badge bg-${cls[d] || 'secondary'}">${map[d] || d}</span>`;
                        }
                    },
                    {
                        data: 'total_values',
                        className: 'text-center',
                        render: d => `<span class="badge bg-secondary">${d}</span>`
                    },
                    { data: 'sort_order', className: 'text-center' },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: (d, t, row) =>
                            `<button class="btn btn-outline-secondary btn-sm me-1" title="Ver valores" onclick="modalValoresAtributo(${row.id},'${escHtml(row.name)}')">
                                <i class="bi bi-list-ul"></i>
                             </button>
                             <button class="btn bg-indigo-dark text-white btn-sm me-1" title="Editar" onclick="modalEditarAtributo(${row.id})">
                                <i class="bi bi-pencil-fill"></i>
                             </button>
                             <button class="btn btn-danger text-white btn-sm" title="Eliminar" onclick="confirmarEliminarAtributo(${row.id},'${escHtml(row.name)}')">
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

function htmlFormAtributo(data) {
    const d = data || {};
    const tipos = ['select', 'color', 'text'];
    const labels = ['Select (lista desplegable)', 'Color (paleta de colores)', 'Texto (valor libre)'];
    const opts = tipos.map((t, i) => `<option value="${t}" ${d.type === t ? 'selected' : ''}>${labels[i]}</option>`).join('');
    return `
        <div class="text-start">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input id="attrName" class="form-control" placeholder="Ej: Talla"
                       value="${escHtml(d.name || '')}">
            </div>
            <div class="row g-2">
                <div class="col-md-7 mb-3">
                    <label class="form-label fw-semibold">Tipo</label>
                    <select id="attrType" class="form-select">${opts}</select>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label fw-semibold">Orden</label>
                    <input id="attrOrder" type="number" class="form-control"
                           value="${d.sort_order !== undefined ? d.sort_order : 0}" min="0">
                </div>
            </div>
        </div>`;
}

function getAttrFormValues() {
    const name = document.getElementById('attrName').value.trim();
    if (!name) { Swal.showValidationMessage('El nombre del atributo es obligatorio'); return false; }
    return {
        name,
        type:       document.getElementById('attrType').value,
        sort_order: document.getElementById('attrOrder').value || 0,
    };
}

function modalAgregarAtributo() {
    Swal.fire({
        title: '<i class="bi bi-palette-fill text-primary me-2"></i>Nuevo Atributo',
        width: 520,
        html: htmlFormAtributo(),
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-save me-1"></i> Guardar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#0d6efd',
        preConfirm: getAttrFormValues
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_product_attributes.php', { action: 'create_attribute', ...result.value }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: '¡Guardado!', text: resp.message, timer: 1800, showConfirmButton: false });
                    cargarAtributos();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
}

function modalEditarAtributo(id) {
    const attr = atributosLista.find(a => a.id == id);
    if (!attr) return;
    Swal.fire({
        title: '<i class="bi bi-pencil-fill text-warning me-2"></i>Editar Atributo',
        width: 520,
        html: htmlFormAtributo(attr),
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-save me-1"></i> Actualizar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#fd7e14',
        preConfirm: getAttrFormValues
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_product_attributes.php', { action: 'update_attribute', id, ...result.value }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: '¡Actualizado!', text: resp.message, timer: 1800, showConfirmButton: false });
                    cargarAtributos();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
}

function confirmarEliminarAtributo(id, nombre) {
    Swal.fire({
        title: '¿Eliminar atributo?',
        html: `<p>¿Seguro que deseas eliminar el atributo <strong>${escHtml(nombre)}</strong>?</p>
               <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>También se eliminarán todos sus valores.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Eliminar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#dc3545',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_product_attributes.php', { action: 'delete_attribute', id }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Eliminado', text: resp.message, timer: 1800, showConfirmButton: false });
                    cargarAtributos();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
}

function modalValoresAtributo(attrId, attrName) {
    const attr = atributosLista.find(a => a.id == attrId);
    const attrType = attr ? attr.type : 'select';
    const isColor = attrType === 'color';

    $.get('components/products/api_product_attributes.php', { action: 'get_values', attribute_id: attrId }, null, 'json')
        .done(resp => {
            const valores = resp.success ? resp.data : [];
            let valoresHtml = '';
            valores.forEach(v => {
                const colorCell = isColor
                    ? `<td class="text-center small">${v.color_hex ? '<span class="d-inline-block me-1 rounded-circle" style="width:14px;height:14px;background:'+escHtml(v.color_hex)+';border:1px solid #ccc;"></span><code>'+escHtml(v.color_hex)+'</code>' : '—'}</td>`
                    : '';
                const colorBadge = (isColor && v.color_hex)
                    ? `<span class="d-inline-block me-1 rounded-circle" style="width:14px;height:14px;background:${escHtml(v.color_hex)};border:1px solid #ccc;"></span>`
                    : '';
                valoresHtml += `
                    <tr data-vid="${v.id}">
                        <td>${colorBadge}${escHtml(v.value)}</td>
                        ${colorCell}
                        <td class="text-center">${v.sort_order}</td>
                        <td class="text-center">
                            <button class="btn btn-outline-warning btn-sm py-0 px-1 me-1" title="Editar" onclick="modalEditarValor(${v.id},${attrId},'${escHtml(v.value)}','${escHtml(v.color_hex||'')}',${v.sort_order},'${attrType}')">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm py-0 px-1" title="Eliminar" onclick="confirmarEliminarValor(${v.id},'${escHtml(v.value)}',${attrId})">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>`;
            });
            if (!valoresHtml) {
                const colspan = isColor ? 4 : 3;
                valoresHtml = `<tr><td colspan="${colspan}" class="text-center text-muted py-3">Sin valores definidos</td></tr>`;
            }

            const colorTh = isColor ? '<th style="width:110px" class="text-center">Color</th>' : '';
            const nameCol = isColor ? 'col-md-5' : 'col-md-8';
            const colorInputCol = isColor
                ? `<div class="col-md-3"><input id="newValColor" class="form-control form-control-sm" placeholder="#FF0000"></div>`
                : '';

            const html = `
                <div class="text-start">
                    <div class="table-responsive" style="max-height:350px;overflow-y:auto;">
                        <table class="table table-sm table-bordered mb-3">
                            <thead class="table-light small"><tr><th>Valor</th>${colorTh}<th style="width:60px" class="text-center">Orden</th><th style="width:80px" class="text-center">Acc.</th></tr></thead>
                            <tbody>${valoresHtml}</tbody>
                        </table>
                    </div>
                    <hr class="my-2">
                    <p class="fw-semibold small mb-2"><i class="bi bi-plus-circle text-success me-1"></i>Agregar nuevo valor${isColor ? '' : ' <span class="text-muted fw-normal">— tipo: '+attrType+'</span>'}</p>
                    <div class="row g-2">
                        <div class="${nameCol}">
                            <input id="newValName" class="form-control form-control-sm" placeholder="${isColor ? 'Ej: Rojo' : 'Ej: S, M, L'}">
                        </div>
                        ${colorInputCol}
                        <div class="col-md-2">
                            <input id="newValOrder" type="number" class="form-control form-control-sm" value="0" min="0">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success btn-sm w-100" onclick="agregarValor(${attrId},'${attrType}')">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>`;

            Swal.fire({
                title: `<i class="bi bi-list-ul text-info me-2"></i>Valores de: ${escHtml(attrName)}
                       <span class="badge bg-${isColor ? 'primary' : (attrType === 'text' ? 'dark' : 'secondary')} ms-2 small">${isColor ? 'Color' : (attrType === 'text' ? 'Texto' : 'Select')}</span>`,
                width: isColor ? 640 : 520,
                html,
                showCancelButton: false,
                confirmButtonText: '<i class="bi bi-check me-1"></i> Cerrar',
                confirmButtonColor: '#6c757d',
            });
        });
}

function agregarValor(attrId, attrType) {
    const val   = document.getElementById('newValName').value.trim();
    const order = document.getElementById('newValOrder').value || 0;
    if (!val) { Swal.fire({ icon: 'warning', title: 'Valor requerido', text: 'Ingrese el nombre del valor.' }); return; }

    let color = '';
    if (attrType === 'color') {
        const colorEl = document.getElementById('newValColor');
        color = colorEl ? colorEl.value.trim() : '';
    }

    $.post('components/products/api_product_attributes.php', { action: 'add_value', attribute_id: attrId, value: val, color_hex: color, sort_order: order }, null, 'json')
        .done(resp => {
            if (resp.success) {
                Swal.fire({ icon: 'success', title: 'Agregado', timer: 1200, showConfirmButton: false });
                const attr = atributosLista.find(a => a.id == attrId);
                modalValoresAtributo(attrId, attr ? attr.name : 'Atributo');
                cargarAtributos();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
            }
        });
}

function modalEditarValor(id, attrId, currentValue, currentColor, currentOrder, attrType) {
    const isColorType = attrType === 'color';
    const colorFieldHtml = isColorType
        ? `<div class="mb-2">
                <label class="form-label fw-semibold small">Color Hex</label>
                <input id="editValColor" class="form-control form-control-sm" value="${escHtml(currentColor)}" placeholder="#FF0000">
                ${currentColor ? `<small class="text-muted">Vista previa: <span class="d-inline-block rounded-circle align-middle" style="width:16px;height:16px;background:${escHtml(currentColor)};border:1px solid #ccc;"></span></small>` : ''}
            </div>`
        : '';

    Swal.fire({
        title: 'Editar valor',
        html: `
            <div class="text-start">
                <div class="mb-2">
                    <label class="form-label fw-semibold small">Valor</label>
                    <input id="editValName" class="form-control form-control-sm" value="${escHtml(currentValue)}">
                </div>
                ${colorFieldHtml}
                <div class="mb-2">
                    <label class="form-label fw-semibold small">Orden</label>
                    <input id="editValOrder" type="number" class="form-control form-control-sm" value="${currentOrder}" min="0">
                </div>
            </div>`,
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        confirmButtonColor: '#fd7e14',
        preConfirm: () => {
            const v = document.getElementById('editValName').value.trim();
            if (!v) { Swal.showValidationMessage('El valor es obligatorio'); return false; }
            const result = {
                value: v,
                sort_order: document.getElementById('editValOrder').value || 0,
            };
            if (isColorType) {
                result.color_hex = document.getElementById('editValColor').value.trim();
            } else {
                result.color_hex = '';
            }
            return result;
        }
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_product_attributes.php', { action: 'update_value', id, ...result.value }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Actualizado', timer: 1200, showConfirmButton: false });
                    const attr = atributosLista.find(a => a.id == attrId);
                    modalValoresAtributo(attrId, attr ? attr.name : 'Atributo');
                    cargarAtributos();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            });
    });
}

function confirmarEliminarValor(id, valor, attrId) {
    Swal.fire({
        title: '¿Eliminar valor?',
        html: `<p>¿Eliminar <strong>${escHtml(valor)}</strong>?</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        confirmButtonColor: '#dc3545',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_product_attributes.php', { action: 'delete_value', id }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1200, showConfirmButton: false });
                    const attr = atributosLista.find(a => a.id == attrId);
                    modalValoresAtributo(attrId, attr ? attr.name : 'Atributo');
                    cargarAtributos();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            });
    });
}

/* ================================================================
   VARIANTES
================================================================ */
let variantsData     = [];
let editingProductId = null;

function toggleVariantSection() {
    const checked = document.getElementById('prodHasVariants').checked;
    document.getElementById('variantSection').style.display = checked ? 'block' : 'none';

    const stockEl = document.getElementById('prodStock');
    const stockNote = document.getElementById('stockManagedNote');
    if (stockEl && stockNote) {
        if (checked) {
            stockEl.readOnly = true;
            stockEl.classList.add('bg-light', 'text-muted');
            stockNote.classList.remove('d-none');
        } else {
            stockEl.readOnly = false;
            stockEl.classList.remove('bg-light', 'text-muted');
            stockNote.classList.add('d-none');
        }
    }

    if (checked) {
        cargarAttrCheckboxes();
        if (editingProductId) cargarVariantesEnFormulario();
    }
}

function cargarAttrCheckboxes() {
    $.get('components/products/api_product_attributes.php', { action: 'get' }, null, 'json')
        .done(resp => {
            if (!resp.success) return;
            const container = document.getElementById('attrCheckboxes');
            const attrs = resp.data;
            if (attrs.length === 0) {
                container.innerHTML = '<span class="text-muted small fst-italic">No hay atributos. Cree atributos primero desde la pestaña Atributos.</span>';
                return;
            }

            const typeLabels = { select: 'Select', color: 'Color', text: 'Texto' };
            const typeBadges = { select: 'secondary', color: 'primary', text: 'dark' };

            let html = '';
            attrs.forEach(attr => {
                const badge = typeBadges[attr.type] || 'secondary';
                const label = typeLabels[attr.type] || attr.type;
                const placeholder = attr.type === 'color' ? 'Rojo, Azul, #FF0000, Verde' : 'S, M, L';
                html += `
                    <div class="attr-row border rounded p-2 mb-2 bg-white">
                        <div class="form-check mb-0">
                            <input class="form-check-input attr-check" type="checkbox" id="attr_${attr.id}" value="${attr.id}"
                                   onchange="toggleAttrValuesInput(${attr.id})">
                            <label class="form-check-label small fw-semibold" for="attr_${attr.id}">
                                ${escHtml(attr.name)}
                                <span class="badge bg-${badge} ms-1" style="font-size:0.6rem;">${label}</span>
                            </label>
                        </div>
                        <div class="attr-values-input mt-1" id="attrValues_${attr.id}" style="display:none;">
                            <input type="text" class="form-control form-control-sm attr-values-text"
                                   id="attrValuesText_${attr.id}" placeholder="${placeholder}"
                                   style="font-size:0.75rem;">
                            <small class="text-muted" style="font-size:0.65rem;">Separe los valores con coma. ${attr.type === 'color' ? 'Use #hex para colores.' : ''}</small>
                        </div>
                    </div>`;
            });
            container.innerHTML = html;
        });
}

function toggleAttrValuesInput(attrId) {
    const cb    = document.getElementById('attr_' + attrId);
    const input = document.getElementById('attrValues_' + attrId);
    if (input) {
        input.style.display = cb.checked ? 'block' : 'none';
        if (!cb.checked) {
            const textEl = document.getElementById('attrValuesText_' + attrId);
            if (textEl) textEl.value = '';
        }
    }
}

function generarVariantes() {
    const checkedCbs = [...document.querySelectorAll('.attr-check:checked')];
    if (checkedCbs.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Sin atributos', text: 'Seleccione al menos un atributo e ingrese valores para generar variantes.' });
        return;
    }

    if (!editingProductId) {
        Swal.fire({ icon: 'info', title: 'Producto no guardado', text: 'Primero guarde el producto, luego podrá generar sus variantes.' });
        return;
    }

    const attributesData = [];
    let totalCombos = 1;
    let errores = false;

    checkedCbs.forEach(cb => {
        const attrId = cb.value;
        const textEl = document.getElementById('attrValuesText_' + attrId);
        const raw     = textEl ? textEl.value.trim() : '';

        if (!raw) {
            const label = cb.parentElement.querySelector('label');
            const name  = label ? label.textContent.trim().split(' ')[0] : ('ID ' + attrId);
            Swal.fire({ icon: 'warning', title: 'Faltan valores', text: 'Ingrese valores para: ' + name });
            errores = true;
            return;
        }

        const values = raw.split(',').map(v => v.trim()).filter(v => v !== '');
        if (values.length === 0) {
            const label = cb.parentElement.querySelector('label');
            const name  = label ? label.textContent.trim().split(' ')[0] : ('ID ' + attrId);
            Swal.fire({ icon: 'warning', title: 'Valores inválidos', text: 'Ingrese al menos un valor para: ' + name });
            errores = true;
            return;
        }

        attributesData.push({ attribute_id: parseInt(attrId), values });
        totalCombos *= values.length;
    });

    if (errores) return;

    const previewText = `Se crearán <strong>${totalCombos}</strong> variante(s) combinando los atributos seleccionados.`;

    Swal.fire({
        title: '<i class="bi bi-magic text-success me-2"></i>Generar variantes',
        html: `<p>${previewText}</p>
               <p class="text-muted small mb-0">Cada variante inicia con <strong>stock 0</strong>. El precio se hereda del producto. Debe digitar el stock de cada variante manualmente.</p>`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-magic me-1"></i> Generar ahora',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#198754',
    }).then(confirmResult => {
        if (!confirmResult.isConfirmed) return;

        $.post('components/products/api_product_variants.php', {
            action: 'generate',
            product_id: editingProductId,
            attributes_data: JSON.stringify(attributesData)
        }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Variantes generadas', text: resp.message, timer: 1500, showConfirmButton: false });
                    cargarVariantesEnFormulario();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.' }));
    });
}

function cargarVariantesEnFormulario() {
    if (!editingProductId) return;

    $.get('components/products/api_product_variants.php', { action: 'get', product_id: editingProductId }, null, 'json')
        .done(resp => {
            if (!resp.success) return;
            variantsData = resp.data;
            renderVariantsTable();
        });
}

function renderVariantsTable() {
    const tbody = document.getElementById('variantsTableBody');
    const emptyRow = document.getElementById('variantsEmptyRow');

    if (variantsData.length === 0) {
        tbody.innerHTML = '';
        if (emptyRow) {
            emptyRow.style.display = '';
            tbody.appendChild(emptyRow);
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted small py-3"><i class="bi bi-info-circle me-1"></i>Seleccione atributos y presione "Generar combinaciones"</td></tr>';
        }
        document.getElementById('variantsJson').value = '';
        return;
    }

    if (emptyRow) emptyRow.remove();

    let html = '';
    let totalStock = 0;
    variantsData.forEach((v, i) => {
        totalStock += parseInt(v.stock) || 0;
        html += `
            <tr>
                <td class="small fw-semibold">${escHtml(v.attributes_display || v.name || '—')}</td>
                <td><input type="text" class="form-control form-control-sm var-sku" data-idx="${i}" value="${escHtml(v.sku)}" style="font-size:0.75rem;"></td>
                <td><input type="number" step="0.01" min="0" class="form-control form-control-sm var-price" data-idx="${i}" value="${v.price || ''}" style="font-size:0.75rem;"></td>
                <td><input type="number" min="0" class="form-control form-control-sm var-stock" data-idx="${i}" value="${v.stock || 0}" style="font-size:0.75rem;width:65px;"></td>
                <td class="text-center">
                    <select class="form-select form-select-sm var-active" data-idx="${i}" style="font-size:0.7rem;padding:2px 4px;">
                        <option value="1" ${v.is_active == 1 ? 'selected' : ''}>Sí</option>
                        <option value="0" ${v.is_active == 0 ? 'selected' : ''}>No</option>
                    </select>
                </td>
                <td class="text-center">
                    <button class="btn btn-outline-danger btn-sm py-0 px-1" title="Eliminar" onclick="eliminarVarianteDelForm(${v.id},${i})">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </td>
            </tr>`;
    });
    html += `<tr class="table-light fw-semibold small">
        <td><i class="bi bi-calculator me-1"></i>Total</td>
        <td>${variantsData.length} variantes</td>
        <td></td>
        <td class="text-primary">${totalStock} unds.</td>
        <td colspan="2"><small class="text-muted">Stock auto-sincronizado</small></td>
    </tr>`;
    tbody.innerHTML = html;

    document.getElementById('variantsJson').value = JSON.stringify(variantsData.map(v => v.id));
}

function eliminarVarianteDelForm(variantId, idx) {
    if (!editingProductId) return;
    $.post('components/products/api_product_variants.php', { action: 'delete', id: variantId }, null, 'json')
        .done(resp => {
            if (resp.success) {
                Swal.fire({ icon: 'success', title: 'Eliminada', timer: 1000, showConfirmButton: false });
                cargarVariantesEnFormulario();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
            }
        });
}

function guardarCambiosVariantes() {
    if (!editingProductId || variantsData.length === 0) return $.Deferred().resolve().promise();

    const skuInputs   = document.querySelectorAll('.var-sku');
    const priceInputs = document.querySelectorAll('.var-price');
    const stockInputs = document.querySelectorAll('.var-stock');
    const activeSels  = document.querySelectorAll('.var-active');

    const promises = [];
    skuInputs.forEach(input => {
        const idx = parseInt(input.dataset.idx);
        const v   = variantsData[idx];
        if (!v) return;

        const sku    = input.value.trim();
        const price  = priceInputs[idx].value;
        const stock  = stockInputs[idx].value;
        const active = activeSels[idx].value;

        if (!sku) return;

        const deferred = $.Deferred();
        $.post('components/products/api_product_variants.php', {
            action: 'update',
            id: v.id,
            sku,
            price: price !== '' ? price : '',
            stock: stock !== '' ? stock : 0,
            is_active: active,
            name: v.name || '',
        }, null, 'json')
            .done(r => deferred.resolve(r))
            .fail(() => deferred.resolve({ success: false }));
        promises.push(deferred.promise());
    });

    return $.when.apply($, promises);
}

/* ================================================================
   INICIALIZACIÓN
================================================================ */
$(document).ready(function () {
    cargarCategorias();

    $('#btn-tab-prod').on('shown.bs.tab', function () {
        if (!dtProductos) {
            cargarProductos();
        }
    });

    $('#btn-tab-attrs').on('shown.bs.tab', function () {
        if (!dtAtributos) {
            cargarAtributos();
        }
    });
});
</script>
