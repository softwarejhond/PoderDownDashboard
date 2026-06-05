<?php
// components/products/products_photos.php
// Incluido en fotos_producto.php — requiere $conn disponible

$productId  = isset($_GET['id'])  ? (int)trim($_GET['id'])  : 0;
$productSku = isset($_GET['sku']) ? trim($_GET['sku'])       : '';

$product = null;
if ($productId) {
    $res     = mysqli_query($conn, "SELECT id, sku, name FROM products WHERE id=$productId LIMIT 1");
    $product = mysqli_fetch_assoc($res);
} elseif ($productSku !== '') {
    $skuE    = mysqli_real_escape_string($conn, $productSku);
    $res     = mysqli_query($conn, "SELECT id, sku, name FROM products WHERE sku='$skuE' LIMIT 1");
    $product = mysqli_fetch_assoc($res);
}
?>

<?php if (!$product): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mt-3">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div>
        Producto no encontrado. Verifica la URL o
        <a href="listProducts.php" class="alert-link">vuelve al listado de productos</a>.
    </div>
</div>
<?php else: ?>

<!-- Cabecera del producto -->
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <div>
        <p class="mb-0 text-muted small">Gestionando imágenes de</p>
        <h5 class="mb-0 fw-bold"><?= htmlspecialchars($product['name'], ENT_QUOTES) ?></h5>
        <code class="text-primary"><?= htmlspecialchars($product['sku'], ENT_QUOTES) ?></code>
    </div>
    <a href="listProducts.php" class="btn btn-outline-secondary btn-sm ms-auto">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
</div>

<div class="row g-3">

    <!-- ============================================================
         COLUMNA IZQUIERDA — ZONA DE SUBIDA
    ============================================================ -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-cloud-upload me-2 text-primary"></i>Subir imágenes
            </div>
            <div class="card-body d-flex flex-column">

                <!-- Drop zone -->
                <div id="dropZone"
                     class="photos-drop-zone flex-grow-1 d-flex flex-column align-items-center justify-content-center p-4 rounded text-center mb-3 w-100">
                    <i class="bi bi-images fs-1 text-muted mb-2"></i>
                    <p class="fw-semibold text-muted mb-1">Arrastra imágenes aquí</p>
                    <p class="text-muted small mb-3">o haz clic para seleccionar</p>
                    <p class="small mb-3">
                        <span class="text-success me-3">
                            <i class="bi bi-check-circle-fill me-1"></i>PNG · JPG · JPEG · WEBP
                        </span>
                        <span class="text-danger">
                            <i class="bi bi-x-circle-fill me-1"></i>GIF no permitido
                        </span>
                    </p>
                    <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="document.getElementById('fileInput').click()">
                        <i class="bi bi-folder2-open me-1"></i> Seleccionar archivos
                    </button>
                    <input type="file" id="fileInput" multiple accept=".png,.jpg,.jpeg,.webp" class="d-none">
                </div>

                <!-- Previsualización antes de subir -->
                <div id="previewArea" class="d-none">
                    <p class="fw-semibold small mb-2">
                        <i class="bi bi-check2-all text-success me-1"></i>Archivos listos para subir:
                    </p>
                    <div id="previewList" class="d-flex flex-wrap gap-2 mb-3"></div>
                    <div class="d-flex gap-2">
                        <button id="btnUpload" class="btn btn-success btn-sm flex-grow-1" onclick="subirImagenes()">
                            <i class="bi bi-cloud-upload me-1"></i>
                            Subir <span id="uploadCount">0</span> archivo(s)
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="limpiarSeleccion()" title="Cancelar selección">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Barra de progreso -->
                <div id="uploadProgress" class="d-none mt-2">
                    <div class="progress" style="height:8px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success w-100"></div>
                    </div>
                    <p class="text-muted small text-center mt-1 mb-0">Subiendo imágenes…</p>
                </div>

            </div>
        </div>
    </div>

    <!-- ============================================================
         COLUMNA DERECHA — GALERÍA / ORDEN
    ============================================================ -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <span class="fw-semibold">
                    <i class="bi bi-grid-3x3-gap me-2 text-success"></i>Galería del producto
                </span>
                <span class="badge bg-secondary" id="fotosCount">0</span>
            </div>
            <div class="card-body p-2 d-flex flex-column">
                <p class="text-muted small text-center mb-2">
                    <i class="bi bi-arrows-move me-1"></i>Arrastra para reordenar · La primera imagen es la principal
                </p>
                <div id="galeriaLista" class="photos-gallery-list"></div>
                <div id="galeriaVacia" class="text-center text-muted py-5">
                    <i class="bi bi-image fs-2 d-block mb-2"></i>
                    Sin imágenes. Sube la primera foto desde el panel izquierdo.
                </div>
            </div>
        </div>
    </div>

</div><!-- /row -->

<!-- ================================================================
     ESTILOS
================================================================ -->
<style>
.photos-drop-zone {
    border: 2px dashed #ced4da;
    min-height: 240px;
    cursor: pointer;
    transition: background .2s, border-color .2s;
    background: #f8f9fa;
    border-radius: 8px !important;
}
.photos-drop-zone.drag-over {
    background: #e8f4ff;
    border-color: #0d6efd;
}

.photos-gallery-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-height: 60px;
}

.photo-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 6px 10px;
    cursor: grab;
    transition: box-shadow .15s, border-color .15s;
}
.photo-item:hover        { border-color: #adb5bd; }
.photo-item:active       { cursor: grabbing; }
.photo-item.is-primary   { border-color: #ffc107; background: #fffdf0; }
.photo-item.sortable-ghost  { opacity: .3; background: #e9ecef; }
.photo-item.sortable-chosen { box-shadow: 0 6px 16px rgba(0,0,0,.15); }

.photo-item img {
    width: 56px;
    height: 56px;
    object-fit: cover;
    border-radius: 6px;
    flex-shrink: 0;
    border: 1px solid #dee2e6;
}

.photo-item .photo-info {
    flex: 1;
    min-width: 0;
}
.photo-item .photo-name {
    font-size: .78rem;
    color: #555;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.preview-thumb {
    position: relative;
    width: 72px;
    height: 72px;
}
.preview-thumb img {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}
.preview-thumb .btn-rm {
    position: absolute;
    top: -7px;
    right: -7px;
    width: 20px;
    height: 20px;
    font-size: 10px;
    padding: 0;
    line-height: 1;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<!-- ================================================================
     JAVASCRIPT
================================================================ -->
<script>
const PRODUCT_ID  = <?= (int)$product['id'] ?>;
const PRODUCT_SKU = '<?= addslashes(htmlspecialchars($product['sku'], ENT_QUOTES)) ?>';
let selectedFiles   = [];
let sortableGaleria = null;

/* ----------------------------------------------------------------
   DROP ZONE — eventos
---------------------------------------------------------------- */
const dropZone  = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.classList.add('drag-over');
});
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    agregarArchivos(Array.from(e.dataTransfer.files));
});
dropZone.addEventListener('click', e => {
    if (!e.target.closest('button') && e.target.id !== 'fileInput') {
        fileInput.click();
    }
});
fileInput.addEventListener('change', () => {
    agregarArchivos(Array.from(fileInput.files));
    fileInput.value = '';
});

function agregarArchivos(files) {
    const allowed    = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
    const rechazados = [];
    files.forEach(f => {
        if (!allowed.includes(f.type)) {
            rechazados.push(f.name);
        } else {
            selectedFiles.push(f);
        }
    });
    if (rechazados.length) {
        Swal.fire({
            icon: 'warning',
            title: 'Formato no permitido',
            html: `<p class="mb-2">Solo se aceptan <strong>PNG, JPG, JPEG y WEBP</strong>.<br>No se permiten GIF ni otros formatos.</p>
                   <ul class="text-start small list-unstyled">
                     ${rechazados.map(n => `<li><i class="bi bi-x-circle-fill text-danger me-1"></i>${n}</li>`).join('')}
                   </ul>`,
        });
    }
    renderizarPreviews();
}

function renderizarPreviews() {
    const area  = document.getElementById('previewArea');
    const list  = document.getElementById('previewList');
    const count = document.getElementById('uploadCount');

    if (selectedFiles.length === 0) {
        area.classList.add('d-none');
        list.innerHTML = '';
        return;
    }
    area.classList.remove('d-none');
    count.textContent = selectedFiles.length;
    list.innerHTML    = '';

    selectedFiles.forEach((f, i) => {
        const reader = new FileReader();
        reader.onload = ev => {
            const div = document.createElement('div');
            div.className = 'preview-thumb';
            div.innerHTML = `
                <img src="${ev.target.result}" alt="${f.name}" title="${f.name}">
                <button class="btn btn-danger btn-rm" onclick="quitarArchivo(${i})" title="Quitar">×</button>`;
            list.appendChild(div);
        };
        reader.readAsDataURL(f);
    });
}

function quitarArchivo(i) {
    selectedFiles.splice(i, 1);
    renderizarPreviews();
}
function limpiarSeleccion() {
    selectedFiles = [];
    renderizarPreviews();
}

/* ----------------------------------------------------------------
   SUBIR IMÁGENES al servidor
---------------------------------------------------------------- */
function subirImagenes() {
    if (selectedFiles.length === 0) return;

    const progress  = document.getElementById('uploadProgress');
    const btnUpload = document.getElementById('btnUpload');

    btnUpload.disabled = true;
    progress.classList.remove('d-none');

    const formData = new FormData();
    formData.append('action',     'upload');
    formData.append('product_id', PRODUCT_ID);
    formData.append('sku',        PRODUCT_SKU);
    selectedFiles.forEach(f => formData.append('images[]', f));

    $.ajax({
        url: 'components/products/api_product_images.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (resp) {
            progress.classList.add('d-none');
            btnUpload.disabled = false;

            if (resp.success || (resp.uploaded && resp.uploaded.length > 0)) {
                limpiarSeleccion();
                cargarGaleria();
                if (resp.errors && resp.errors.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Subida parcial',
                        html: `<p>${resp.message}</p>
                               <ul class="text-start small list-unstyled">
                                 ${resp.errors.map(e => `<li><i class="bi bi-exclamation-circle text-warning me-1"></i>${e}</li>`).join('')}
                               </ul>`,
                    });
                } else {
                    Swal.fire({ icon: 'success', title: '¡Subido!', text: resp.message, timer: 1800, showConfirmButton: false });
                }
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: resp.message || 'No se pudieron subir las imágenes.' });
            }
        },
        error: function () {
            progress.classList.add('d-none');
            btnUpload.disabled = false;
            Swal.fire({ icon: 'error', title: 'Error de red', text: 'No fue posible conectar con el servidor.' });
        }
    });
}

/* ----------------------------------------------------------------
   GALERÍA — cargar y renderizar
---------------------------------------------------------------- */
function cargarGaleria() {
    $.get('components/products/api_product_images.php', { action: 'get', product_id: PRODUCT_ID }, null, 'json')
        .done(function (resp) {
            const lista   = document.getElementById('galeriaLista');
            const vacia   = document.getElementById('galeriaVacia');
            const counter = document.getElementById('fotosCount');

            lista.innerHTML = '';

            if (!resp.success || resp.data.length === 0) {
                vacia.classList.remove('d-none');
                counter.textContent = '0';
                return;
            }

            vacia.classList.add('d-none');
            counter.textContent = resp.data.length;

            resp.data.forEach(img => {
                const isPrimary = img.is_primary == 1;
                const item      = document.createElement('div');
                item.className  = 'photo-item' + (isPrimary ? ' is-primary' : '');
                item.dataset.id = img.id;
                item.innerHTML  = `
                    <i class="bi bi-grip-vertical text-muted flex-shrink-0"></i>
                    <img src="${img.image_path}" alt="${img.alt_text || ''}">
                    <div class="photo-info">
                        ${isPrimary
                            ? '<span class="badge bg-warning text-dark small d-block mb-1"><i class="bi bi-star-fill me-1"></i>Principal</span>'
                            : ''}
                        <div class="photo-name" title="${img.image_path}">${img.alt_text || 'Sin descripción'}</div>
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0">
                        ${!isPrimary
                            ? `<button class="btn btn-outline-warning btn-sm py-0 px-2" title="Marcar como principal"
                                       onclick="setPrimary(${img.id})">
                                   <i class="bi bi-star"></i>
                               </button>`
                            : ''}
                        <button class="btn btn-outline-danger btn-sm py-0 px-2" title="Eliminar"
                                onclick="eliminarImagen(${img.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>`;
                lista.appendChild(item);
            });

            // SortableJS — reordenamiento drag & drop
            if (sortableGaleria) sortableGaleria.destroy();
            sortableGaleria = new Sortable(lista, {
                animation:   150,
                ghostClass:  'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd:       guardarOrden,
            });
        })
        .fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar la galería.' }));
}

function guardarOrden() {
    const ids = [...document.querySelectorAll('#galeriaLista .photo-item')].map(el => el.dataset.id);
    $.post('components/products/api_product_images.php', { action: 'reorder', 'order[]': ids });
}

function setPrimary(id) {
    $.post('components/products/api_product_images.php',
        { action: 'set_primary', id, product_id: PRODUCT_ID }, null, 'json')
        .done(resp => {
            if (resp.success) cargarGaleria();
            else Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
        });
}

function eliminarImagen(id) {
    Swal.fire({
        title: '¿Eliminar imagen?',
        text:  'Esta acción no se puede deshacer.',
        icon:  'warning',
        showCancelButton:   true,
        confirmButtonText:  '<i class="bi bi-trash me-1"></i> Eliminar',
        cancelButtonText:   'Cancelar',
        confirmButtonColor: '#dc3545',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('components/products/api_product_images.php', { action: 'delete', id }, null, 'json')
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Eliminada', text: resp.message, timer: 1500, showConfirmButton: false });
                    cargarGaleria();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            });
    });
}

/* ---- Inicialización ---- */
$(document).ready(() => cargarGaleria());
</script>

<?php endif; ?>
