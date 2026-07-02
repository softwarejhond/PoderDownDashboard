<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>

<style>
#galeriaTabsWrapper { width: 100%; }
#galeriaTabsWrapper .card-header,
#galeriaTabsWrapper .card-body,
#galeriaTabsWrapper .tab-content,
#galeriaTabsWrapper .tab-pane { width: 100%; }

#galeriaTabs .nav-link { color: #000; }
#galeriaTabs .nav-link:hover { color: #000; }
#galeriaTabs .nav-link.active { color: var(--bs-indigo-dark) !important; font-weight: 600; }
#galeriaTabs .nav-link:disabled { color: #adb5bd; }

.gal-drop-zone {
    border: 2px dashed #ced4da;
    min-height: 200px;
    cursor: pointer;
    transition: background .2s, border-color .2s;
    background: #f8f9fa;
    border-radius: 8px !important;
}
.gal-drop-zone.drag-over { background: #e8f4ff; border-color: #0d6efd; }

.gal-obras-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 60px;
}
.gal-obra-item {
    display: flex;
    width: 100%;
    gap: 16px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 16px;
    transition: box-shadow .15s, border-color .15s;
}
.gal-obra-item:hover { border-color: #adb5bd; }
.gal-obra-item.is-featured { border-color: #ffc107; background: #fffdf0; }
.gal-obra-item.sortable-ghost  { opacity: .3; background: #e9ecef; }
.gal-obra-item.sortable-chosen { box-shadow: 0 6px 16px rgba(0,0,0,.15); }

.gal-obra-item .gal-grip { cursor: grab; align-self: center; font-size: 1.4rem; }
.gal-obra-item .gal-grip:active { cursor: grabbing; }
.gal-obra-item img {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 6px;
    flex-shrink: 0;
    border: 1px solid #dee2e6;
}
.gal-obra-fields { flex: 1; min-width: 0; }
.gal-obra-fields .form-label { margin-bottom: .15rem; font-weight: 600; font-size: .8rem; }

@media (max-width: 575.98px) {
    .gal-obra-item { flex-direction: column; align-items: stretch; }
    .gal-obra-item .gal-grip { align-self: center; }
    .gal-obra-item img { width: 100%; height: 200px; }
}

.gal-preview-thumb { position: relative; width: 80px; height: 80px; }
.gal-preview-thumb img {
    width: 80px; height: 80px; object-fit: cover;
    border-radius: 6px; border: 1px solid #dee2e6;
}
</style>

<div class="card mt-4" id="galeriaTabsWrapper">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="galeriaTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos" type="button" role="tab">
                    <i class="bi bi-card-text"></i> Datos de galería
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="obras-tab" data-bs-toggle="tab" data-bs-target="#obras" type="button" role="tab" disabled>
                    <i class="bi bi-images"></i> Obras <span id="obrasBadge" class="badge bg-secondary ms-1">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gestionar-tab" data-bs-toggle="tab" data-bs-target="#gestionar" type="button" role="tab">
                    <i class="bi bi-collection"></i> Gestionar galerías
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">

            <!-- ============ TAB DATOS ============ -->
            <div class="tab-pane fade show active" id="datos" role="tabpanel">
                <form id="galeriaForm">
                    <input type="hidden" name="id" id="galeriaId">

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="galTitle" class="form-label fw-bold">Título</label>
                            <input type="text" class="form-control" id="galTitle" name="title" placeholder="Nombre de la galería">
                        </div>
                        <div class="col-md-4">
                            <label for="galStatus" class="form-label fw-bold">Estado</label>
                            <select class="form-select" id="galStatus" name="status">
                                <option value="published">Publicado</option>
                                <option value="draft">Borrador</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="galExcerpt" class="form-label fw-bold">Extracto</label>
                        <textarea class="form-control" id="galExcerpt" name="excerpt" rows="3" placeholder="Breve descripción de la galería"></textarea>
                    </div>

                    <div class="alert alert-light border small" id="featuredInfo">
                        <i class="bi bi-star text-warning"></i>
                        La imagen de portada se elige marcando una obra con la estrella en la pestaña <strong>Obras</strong>.
                        <span id="featuredPreviewWrap" class="d-none ms-2">
                            <img id="featuredPreviewImg" src="" alt="Portada" style="height:48px;border-radius:6px;border:1px solid #dee2e6;vertical-align:middle;">
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <button type="button" class="btn bg-indigo-dark text-white d-none" id="btnGoObras">
                            <i class="bi bi-images me-1"></i> Ir a las obras de esta galería
                        </button>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-outline-secondary me-2" id="btnNewGaleria">
                                <i class="bi bi-plus-lg"></i> Nueva
                            </button>
                            <button type="submit" class="btn bg-lime-dark text-white" id="btnSaveGaleria">
                                <i class="bi bi-check-lg"></i> Guardar y continuar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ============ TAB OBRAS ============ -->
            <div class="tab-pane fade" id="obras" role="tabpanel">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white fw-semibold">
                                <i class="bi bi-cloud-upload me-2 text-primary"></i>Subir obras
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div id="galDropZone"
                                     class="gal-drop-zone flex-grow-1 d-flex flex-column align-items-center justify-content-center p-4 rounded text-center mb-3 w-100">
                                    <i class="bi bi-images fs-1 text-muted mb-2"></i>
                                    <p class="fw-semibold text-muted mb-1">Arrastra imágenes aquí</p>
                                    <p class="text-muted small mb-3">o haz clic para seleccionar</p>
                                    <p class="small mb-3">
                                        <span class="text-success me-2"><i class="bi bi-check-circle-fill me-1"></i>WEBP · AVIF</span>
                                        <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Otros no permitidos</span>
                                    </p>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="document.getElementById('galFileInput').click()">
                                        <i class="bi bi-folder2-open me-1"></i> Seleccionar archivos
                                    </button>
                                    <input type="file" id="galFileInput" multiple accept=".webp,.avif,image/webp,image/avif" class="d-none">
                                </div>

                                <div id="galPreviewArea" class="d-none">
                                    <p class="fw-semibold small mb-2">
                                        <i class="bi bi-check2-all text-success me-1"></i>Listas para subir:
                                    </p>
                                    <div id="galPreviewList" class="d-flex flex-wrap gap-2 mb-3"></div>
                                    <div class="d-flex gap-2">
                                        <button id="galBtnUpload" class="btn btn-success btn-sm flex-grow-1" type="button" onclick="galSubirImagenes()">
                                            <i class="bi bi-cloud-upload me-1"></i> Subir <span id="galUploadCount">0</span> archivo(s)
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="galLimpiarSeleccion()" title="Cancelar">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <div id="galUploadProgress" class="d-none mt-2">
                                    <div class="progress" style="height:8px">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success w-100"></div>
                                    </div>
                                    <p class="text-muted small text-center mt-1 mb-0">Subiendo imágenes…</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                                <span class="fw-semibold"><i class="bi bi-grid-3x3-gap me-2 text-success"></i>Obras de la galería</span>
                                <span class="badge bg-secondary" id="obrasCount">0</span>
                            </div>
                            <div class="p-2 d-flex flex-column">
                                <p class="text-muted small text-center mb-2">
                                    <i class="bi bi-arrows-move me-1"></i>Arrastra para reordenar · La estrella define la portada
                                </p>
                                <div id="galObrasLista" class="gal-obras-list"></div>
                                <div id="galObrasVacia" class="text-center text-muted py-5">
                                    <i class="bi bi-image fs-2 d-block mb-2"></i>
                                    Sin obras. Sube la primera imagen desde el panel izquierdo.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============ TAB GESTIONAR ============ -->
            <div class="tab-pane fade" id="gestionar" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-collection"></i> Galerías creadas</h5>
                    <button type="button" class="btn bg-indigo-dark text-white" id="btnRefreshGalerias">
                        <i class="bi bi-arrow-clockwise"></i> Refrescar
                    </button>
                </div>
                <div id="galeriasTableContainer">
                    <div class="text-center text-muted py-4">Cambia a esta pestaña o haz clic en "Refrescar" para cargar las galerías.</div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const GAL_API    = 'components/galeria/api_galeria.php';
const GAL_UPLOAD = 'components/galeria/upload_obra.php';

let galEditingId    = null;
let galSelectedFiles = [];
let galSortable     = null;

/* ---------------- Utilidades ---------------- */
function galEscapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text == null ? '' : text).replace(/[&<>"']/g, m => map[m]);
}
function galSetTab(sel) { document.querySelector(sel).click(); }

/* ---------------- Guardar / crear galería ---------------- */
document.getElementById('galeriaForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const title = document.getElementById('galTitle').value.trim();
    if (!title) {
        Swal.fire({ title: 'Campo obligatorio', text: 'El título es requerido', icon: 'warning' });
        return;
    }

    const formData = new FormData(this);
    formData.append('action', galEditingId ? 'update' : 'create');

    fetch(GAL_API, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                Swal.fire({ title: 'Error', text: data.message, icon: 'error' });
                return;
            }
            if (!galEditingId) {
                galEditingId = data.galeria_id;
                document.getElementById('galeriaId').value = data.galeria_id;
            }
            galEnableObras(true);
            Swal.fire({
                title: 'Guardado',
                text: 'Galería guardada. Ahora puedes gestionar sus obras.',
                icon: 'success',
                timer: 1600,
                showConfirmButton: false
            });
            galCargarObras();
            galSetTab('#obras-tab');
        })
        .catch(() => Swal.fire({ title: 'Error de conexión', text: 'No se pudo conectar al servidor', icon: 'error' }));
});

document.getElementById('btnNewGaleria').addEventListener('click', galResetForm);

function galResetForm() {
    galEditingId = null;
    document.getElementById('galeriaForm').reset();
    document.getElementById('galeriaId').value = '';
    document.getElementById('galStatus').value = 'published';
    document.getElementById('featuredPreviewWrap').classList.add('d-none');
    document.getElementById('featuredPreviewImg').src = '';
    document.getElementById('galObrasLista').innerHTML = '';
    document.getElementById('obrasBadge').textContent = '0';
    document.getElementById('obrasCount').textContent = '0';
    galSelectedFiles = [];
    galRenderPreviews();
    galEnableObras(false);
    document.getElementById('btnSaveGaleria').innerHTML = '<i class="bi bi-check-lg"></i> Guardar y continuar';
    galSetTab('#datos-tab');
    document.getElementById('galTitle').focus();
}

function galEnableObras(enabled) {
    document.getElementById('obras-tab').disabled = !enabled;
    document.getElementById('btnGoObras').classList.toggle('d-none', !enabled);
}

document.getElementById('btnGoObras').addEventListener('click', function () {
    if (!galEditingId) return;
    galSetTab('#obras-tab');
});

/* ---------------- Dropzone ---------------- */
const galDropZone  = document.getElementById('galDropZone');
const galFileInput = document.getElementById('galFileInput');
const GAL_ALLOWED_MIME = ['image/webp', 'image/avif'];
const GAL_ALLOWED_EXT  = ['webp', 'avif'];

galDropZone.addEventListener('dragover', e => { e.preventDefault(); galDropZone.classList.add('drag-over'); });
galDropZone.addEventListener('dragleave', () => galDropZone.classList.remove('drag-over'));
galDropZone.addEventListener('drop', e => {
    e.preventDefault();
    galDropZone.classList.remove('drag-over');
    galAgregarArchivos(Array.from(e.dataTransfer.files));
});
galDropZone.addEventListener('click', e => {
    if (!e.target.closest('button') && e.target.id !== 'galFileInput') galFileInput.click();
});
galFileInput.addEventListener('change', () => {
    galAgregarArchivos(Array.from(galFileInput.files));
    galFileInput.value = '';
});

function galAgregarArchivos(files) {
    const rechazados = [];
    files.forEach(f => {
        const ext = (f.name.split('.').pop() || '').toLowerCase();
        const okMime = GAL_ALLOWED_MIME.includes(f.type) || f.type === '';
        if (!GAL_ALLOWED_EXT.includes(ext) || !okMime) {
            rechazados.push(f.name);
        } else {
            galSelectedFiles.push(f);
        }
    });
    if (rechazados.length) {
        Swal.fire({
            icon: 'warning',
            title: 'Formato no permitido',
            html: `<p class="mb-2">Solo se aceptan imágenes <strong>WEBP</strong> y <strong>AVIF</strong>.</p>
                   <ul class="text-start small list-unstyled">
                     ${rechazados.map(n => `<li><i class="bi bi-x-circle-fill text-danger me-1"></i>${galEscapeHtml(n)}</li>`).join('')}
                   </ul>`,
        });
    }
    galRenderPreviews();
}

function galRenderPreviews() {
    const area  = document.getElementById('galPreviewArea');
    const list  = document.getElementById('galPreviewList');
    const count = document.getElementById('galUploadCount');
    if (galSelectedFiles.length === 0) {
        area.classList.add('d-none');
        list.innerHTML = '';
        return;
    }
    area.classList.remove('d-none');
    count.textContent = galSelectedFiles.length;
    list.innerHTML = '';
    galSelectedFiles.forEach((f, i) => {
        const reader = new FileReader();
        reader.onload = ev => {
            const div = document.createElement('div');
            div.className = 'gal-preview-thumb';
            div.innerHTML = `<img src="${ev.target.result}" alt="${galEscapeHtml(f.name)}" title="${galEscapeHtml(f.name)}">
                <button class="btn btn-danger position-absolute top-0 end-0 rounded-circle p-0"
                        style="width:20px;height:20px;font-size:11px;line-height:1;transform:translate(30%,-30%);"
                        type="button" onclick="galQuitarArchivo(${i})" title="Quitar">&times;</button>`;
            list.appendChild(div);
        };
        reader.readAsDataURL(f);
    });
}
function galQuitarArchivo(i) { galSelectedFiles.splice(i, 1); galRenderPreviews(); }
function galLimpiarSeleccion() { galSelectedFiles = []; galRenderPreviews(); }

/* ---------------- Subir imágenes ---------------- */
function galSubirImagenes() {
    if (!galEditingId) {
        Swal.fire({ title: 'Guarda primero', text: 'Debes guardar la galería antes de subir obras.', icon: 'info' });
        return;
    }
    if (galSelectedFiles.length === 0) return;

    const progress  = document.getElementById('galUploadProgress');
    const btnUpload = document.getElementById('galBtnUpload');
    btnUpload.disabled = true;
    progress.classList.remove('d-none');

    const files = galSelectedFiles.slice();
    let done = 0, errors = [];

    function next() {
        if (done >= files.length) {
            progress.classList.add('d-none');
            btnUpload.disabled = false;
            galLimpiarSeleccion();
            galCargarObras();
            if (errors.length) {
                Swal.fire({
                    icon: 'warning', title: 'Subida parcial',
                    html: `<ul class="text-start small list-unstyled">
                             ${errors.map(e => `<li><i class="bi bi-exclamation-circle text-warning me-1"></i>${galEscapeHtml(e)}</li>`).join('')}
                           </ul>`,
                });
            } else {
                Swal.fire({ icon: 'success', title: '¡Subido!', text: 'Obras subidas correctamente', timer: 1500, showConfirmButton: false });
            }
            return;
        }
        const f  = files[done];
        const fd = new FormData();
        fd.append('galeria_id', galEditingId);
        fd.append('image', f);
        fetch(GAL_UPLOAD, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(resp => { if (!resp.success) errors.push(f.name + ': ' + resp.message); })
            .catch(() => errors.push(f.name + ': error de red'))
            .finally(() => { done++; next(); });
    }
    next();
}

/* ---------------- Cargar / renderizar obras ---------------- */
function galCargarObras() {
    if (!galEditingId) return;
    fetch(GAL_API + '?action=get_obras&galeria_id=' + galEditingId)
        .then(r => r.json())
        .then(resp => {
            const lista = document.getElementById('galObrasLista');
            const vacia = document.getElementById('galObrasVacia');
            const count = document.getElementById('obrasCount');
            const badge = document.getElementById('obrasBadge');
            lista.innerHTML = '';

            const featured = resp.featured_image || '';
            const fWrap = document.getElementById('featuredPreviewWrap');
            const fImg  = document.getElementById('featuredPreviewImg');
            if (featured) { fImg.src = featured; fWrap.classList.remove('d-none'); }
            else { fImg.src = ''; fWrap.classList.add('d-none'); }

            if (!resp.success || resp.data.length === 0) {
                vacia.classList.remove('d-none');
                count.textContent = '0';
                badge.textContent = '0';
                return;
            }
            vacia.classList.add('d-none');
            count.textContent = resp.data.length;
            badge.textContent = resp.data.length;

            resp.data.forEach(obra => {
                const isFeat = obra.is_featured == 1;
                const item = document.createElement('div');
                item.className = 'gal-obra-item' + (isFeat ? ' is-featured' : '');
                item.dataset.id = obra.id;
                item.innerHTML = `
                    <i class="bi bi-grip-vertical text-muted gal-grip"></i>
                    <img src="${galEscapeHtml(obra.img)}" alt="${galEscapeHtml(obra.title)}">
                    <div class="gal-obra-fields">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            ${isFeat ? '<span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Portada</span>' : '<span></span>'}
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm ${isFeat ? 'btn-warning' : 'btn-outline-warning'} py-0 px-2"
                                        type="button" title="Marcar como portada" onclick="galSetFeatured(${obra.id})">
                                    <i class="bi bi-star${isFeat ? '-fill' : ''}"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger py-0 px-2"
                                        type="button" title="Eliminar" onclick="galEliminarObra(${obra.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Título de la obra"
                                   value="${galEscapeHtml(obra.title)}" onblur="galGuardarObra(${obra.id})" data-field="title">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Meta</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Autor · año · técnica"
                                   value="${galEscapeHtml(obra.meta)}" onblur="galGuardarObra(${obra.id})" data-field="meta">
                        </div>
                        <div>
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control form-control-sm" rows="3" placeholder="Descripción de la obra"
                                      onblur="galGuardarObra(${obra.id})" data-field="descripcion">${galEscapeHtml(obra.descripcion)}</textarea>
                        </div>
                    </div>`;
                lista.appendChild(item);
            });

            if (galSortable) galSortable.destroy();
            galSortable = new Sortable(lista, {
                animation: 150,
                handle: '.gal-grip',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: galGuardarOrden,
            });
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar las obras.' }));
}

function galGuardarOrden() {
    const ids = [...document.querySelectorAll('#galObrasLista .gal-obra-item')].map(el => el.dataset.id);
    const fd = new FormData();
    fd.append('action', 'reorder');
    ids.forEach(id => fd.append('order[]', id));
    fetch(GAL_API, { method: 'POST', body: fd });
}

function galGuardarObra(id) {
    const item = document.querySelector(`#galObrasLista .gal-obra-item[data-id="${id}"]`);
    if (!item) return;
    const fd = new FormData();
    fd.append('action', 'update_obra');
    fd.append('id', id);
    fd.append('title', item.querySelector('[data-field="title"]').value);
    fd.append('meta', item.querySelector('[data-field="meta"]').value);
    fd.append('descripcion', item.querySelector('[data-field="descripcion"]').value);
    fetch(GAL_API, { method: 'POST', body: fd });
}

function galSetFeatured(id) {
    const fd = new FormData();
    fd.append('action', 'set_featured');
    fd.append('id', id);
    fd.append('galeria_id', galEditingId);
    fetch(GAL_API, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(resp => { if (resp.success) galCargarObras(); else Swal.fire({ icon: 'error', title: 'Error', text: resp.message }); });
}

function galEliminarObra(id) {
    Swal.fire({
        title: '¿Eliminar obra?', text: 'Esta acción no se puede deshacer.', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', confirmButtonColor: '#dc3545',
    }).then(result => {
        if (!result.isConfirmed) return;
        const fd = new FormData();
        fd.append('action', 'delete_obra');
        fd.append('id', id);
        fetch(GAL_API, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Eliminada', timer: 1200, showConfirmButton: false });
                    galCargarObras();
                } else { Swal.fire({ icon: 'error', title: 'Error', text: resp.message }); }
            });
    });
}

/* ---------------- Gestionar galerías ---------------- */
document.getElementById('btnRefreshGalerias').addEventListener('click', galCargarLista);
document.getElementById('gestionar-tab').addEventListener('shown.bs.tab', galCargarLista);

function galCargarLista() {
    const container = document.getElementById('galeriasTableContainer');
    container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
    fetch(GAL_API + '?action=list')
        .then(r => r.json())
        .then(data => {
            if (!data.success || !data.data.length) {
                container.innerHTML = '<div class="text-center text-muted py-4">No hay galerías registradas.</div>';
                return;
            }
            let html = '<div class="table-responsive"><table class="table table-striped table-hover align-middle" style="width:100%;">';
            html += '<thead class="table-dark"><tr>';
            html += '<th>Portada</th><th>Título</th><th>Autor</th><th>Obras</th><th>Estado</th><th>Fecha</th><th>Acciones</th>';
            html += '</tr></thead><tbody>';
            data.data.forEach(g => {
                const badge = g.status === 'published'
                    ? '<span class="badge bg-success">Publicado</span>'
                    : '<span class="badge bg-warning text-dark">Borrador</span>';
                const cover = g.featured_image
                    ? `<img src="${galEscapeHtml(g.featured_image)}" style="width:52px;height:40px;object-fit:cover;border-radius:5px;border:1px solid #dee2e6;">`
                    : '<span class="text-muted small"><i class="bi bi-image"></i></span>';
                html += '<tr>';
                html += '<td>' + cover + '</td>';
                html += '<td><strong>' + galEscapeHtml(g.title) + '</strong></td>';
                html += '<td>' + galEscapeHtml(g.author) + '</td>';
                html += '<td><span class="badge bg-secondary">' + g.total_obras + '</span></td>';
                html += '<td>' + badge + '</td>';
                html += '<td><small>' + galEscapeHtml(g.created_at) + '</small></td>';
                html += '<td>';
                html += '<button class="btn btn-sm bg-indigo-dark text-white me-1" onclick="galEditar(' + g.id + ')" title="Editar"><i class="bi bi-pencil"></i></button>';
                html += '<button class="btn btn-sm btn-outline-danger" onclick="galEliminar(' + g.id + ')" title="Eliminar"><i class="bi bi-trash"></i></button>';
                html += '</td></tr>';
            });
            html += '</tbody></table></div>';
            container.innerHTML = html;
        })
        .catch(() => { container.innerHTML = '<div class="text-center text-danger py-4">Error al cargar las galerías.</div>'; });
}

function galEditar(id) {
    fetch(GAL_API + '?action=get&id=' + id)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { Swal.fire({ title: 'Error', text: data.message, icon: 'error' }); return; }
            const g = data.data;
            galEditingId = g.id;
            document.getElementById('galeriaId').value = g.id;
            document.getElementById('galTitle').value = g.title;
            document.getElementById('galExcerpt').value = g.excerpt || '';
            document.getElementById('galStatus').value = g.status;
            document.getElementById('btnSaveGaleria').innerHTML = '<i class="bi bi-check-lg"></i> Actualizar';
            galEnableObras(true);
            galCargarObras();
            galSetTab('#datos-tab');
            document.getElementById('galTitle').focus();
        })
        .catch(() => Swal.fire({ title: 'Error de conexión', text: 'No se pudo conectar al servidor', icon: 'error' }));
}

function galEliminar(id) {
    Swal.fire({
        title: '¿Eliminar galería?', text: 'Se eliminarán también todas sus obras e imágenes.', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', confirmButtonColor: '#d33',
    }).then(result => {
        if (!result.isConfirmed) return;
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        fetch(GAL_API, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ title: 'Eliminada', icon: 'success', timer: 1300, showConfirmButton: false });
                    if (galEditingId == id) galResetForm();
                    galCargarLista();
                } else { Swal.fire({ title: 'Error', text: data.message, icon: 'error' }); }
            });
    });
}
</script>
