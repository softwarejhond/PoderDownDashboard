<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>

<style>
#blogTabsWrapper {
    width: 100%;
}
#blogTabsWrapper .card-header,
#blogTabsWrapper .card-body {
    width: 100%;
}
#blogTabsWrapper .tab-content,
#blogTabsWrapper .tab-pane {
    width: 100%;
}
#blogTabsWrapper textarea {
    width: 100% !important;
    display: block;
}
.editor-preview-side {
    width: 50% !important;
}
#gestionar .table {
    width: 100% !important;
    table-layout: fixed;
    margin-bottom: 0;
}
#gestionar .table-responsive {
    width: 100%;
}
</style>

<div class="card mt-4" id="blogTabsWrapper" style="width:100%;">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="blogTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="redactar-tab" data-bs-toggle="tab" data-bs-target="#redactar" type="button" role="tab">
                    <i class="bi bi-pencil-square"></i> Redactar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gestionar-tab" data-bs-toggle="tab" data-bs-target="#gestionar" type="button" role="tab">
                    <i class="bi bi-files"></i> Gestionar blogs
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body" style="width:100%;">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="redactar" role="tabpanel">
                <form id="blogForm">
                    <input type="hidden" name="id" id="postId">
                    <input type="hidden" name="featured_image" id="featuredImage">

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="blogTitle" class="form-label fw-bold">Título</label>
                            <input type="text" class="form-control" id="blogTitle" name="title" placeholder="Título del blog">
                        </div>
                        <div class="col-md-4">
                            <label for="blogStatus" class="form-label fw-bold">Estado</label>
                            <select class="form-select" id="blogStatus" name="status">
                                <option value="draft">Borrador</option>
                                <option value="published">Publicado</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="blogExcerpt" class="form-label fw-bold">Extracto</label>
                        <textarea class="form-control" id="blogExcerpt" name="excerpt" rows="2" placeholder="Breve descripción del blog"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="blogContent" class="form-label fw-bold">Contenido</label>
                        <textarea id="blogContent" name="content" style="width:100%;min-height:350px;"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Imagen de portada</label>
                        <small class="text-muted ms-2">(Recomendado: 16:9)</small>
                        <button type="button" class="btn bg-magenta-dark text-white me-2" id="btnUploadCover">
                            <i class="bi bi-image"></i> Subir portada (PNG)
                        </button>
                        <input type="file" id="coverInput" accept="image/png" style="display:none;">
                        <span id="coverUploadStatus" class="ms-2 text-muted"></span>
                        <div id="coverPreview" class="mt-2" style="display:none;">
                            <img id="coverPreviewImg" src="" alt="Imagen de portada" style="max-width:300px;max-height:200px;" class="img-thumbnail">
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="btnRemoveCover">
                                <i class="bi bi-x-circle"></i> Quitar
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn bg-magenta-dark text-white me-2" id="btnInsertContentImage">
                            <i class="bi bi-image"></i> Insertar imagen en contenido (PNG)
                        </button>
                        <input type="file" id="contentImageInput" accept="image/png" style="display:none;">
                        <span id="contentImageUploadStatus" class="ms-2 text-muted"></span>
                    </div>

                    <div class="mb-3" id="contentImagesSection" style="display:none;">
                        <label class="form-label fw-bold"><i class="bi bi-images"></i> Imágenes en el contenido</label>
                        <div id="contentImages" class="d-flex flex-wrap gap-2 align-items-start"></div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary me-2" id="btnNewPost">
                            <i class="bi bi-plus-lg"></i> Nuevo
                        </button>
                        <button type="submit" class="btn bg-lime-dark text-white" id="btnSavePost">
                            <i class="bi bi-check-lg"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="gestionar" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-files"></i> Posts creados</h5>
                    <button type="button" class="btn bg-indigo-dark text-white" id="btnRefreshList">
                        <i class="bi bi-arrow-clockwise"></i> Refrescar
                    </button>
                </div>
                <div id="postsTableContainer">
                    <div class="text-center text-muted py-4">Haz clic en "Refrescar" o cambia a esta pestaña para cargar los posts.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let easyMDE;
let editingId = null;

function enforceEditorFullWidth() {
    const cmEl = document.querySelector('.EasyMDEContainer .CodeMirror');
    const sizer = document.querySelector('.EasyMDEContainer .CodeMirror-sizer');
    const scrollEl = document.querySelector('.EasyMDEContainer .CodeMirror-scroll');
    const lines = document.querySelector('.EasyMDEContainer .CodeMirror-lines');
    const gutters = document.querySelector('.EasyMDEContainer .CodeMirror-gutters');

    if (cmEl) {
        cmEl.style.setProperty('width', '100%', 'important');
    }
    if (sizer) {
        sizer.style.setProperty('min-width', '100%', 'important');
        sizer.style.setProperty('margin-left', '0', 'important');
        sizer.style.setProperty('padding-right', '0', 'important');
    }
    if (scrollEl) {
        scrollEl.style.setProperty('width', '100%', 'important');
    }
    if (lines) {
        lines.style.setProperty('width', '100%', 'important');
    }
    if (gutters) {
        gutters.style.setProperty('display', 'none', 'important');
    }
    if (easyMDE && easyMDE.codemirror) {
        easyMDE.codemirror.refresh();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    easyMDE = new EasyMDE({
        element: document.getElementById('blogContent'),
        spellChecker: false,
        placeholder: 'Escribe el contenido del blog en Markdown...',
        status: ['lines', 'words', 'cursor'],
        toolbar: [
            'bold', 'italic', 'heading', '|',
            'quote', 'unordered-list', 'ordered-list', '|',
            'link', 'image', '|',
            'preview', 'side-by-side', 'fullscreen', '|',
            'guide'
        ],
        renderingConfig: {
            singleLineBreaks: false,
            codeSyntaxHighlighting: true,
        },
    });

    setTimeout(function() {
        enforceEditorFullWidth();
        easyMDE.codemirror.setSize('100%', 'auto');
    }, 50);

    easyMDE.codemirror.on('change', function() {
        setTimeout(enforceEditorFullWidth, 5);
        clearTimeout(contentImagesDebounce);
        contentImagesDebounce = setTimeout(function() {
            showContentImages(easyMDE.value());
        }, 400);
    });

    const coverInput = document.getElementById('coverInput');
    const coverUploadStatus = document.getElementById('coverUploadStatus');
    const coverPreview = document.getElementById('coverPreview');
    const coverPreviewImg = document.getElementById('coverPreviewImg');
    const featuredImage = document.getElementById('featuredImage');

    document.getElementById('btnUploadCover').addEventListener('click', function() {
        coverInput.click();
    });

    coverInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        if (file.type !== 'image/png' || !file.name.toLowerCase().endsWith('.png')) {
            Swal.fire({ title: 'Error', text: 'Solo se permiten imágenes PNG', icon: 'error', confirmButtonText: 'OK' });
            coverInput.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({ title: 'Error', text: 'La imagen no debe superar los 2 MB', icon: 'error', confirmButtonText: 'OK' });
            coverInput.value = '';
            return;
        }

        coverUploadStatus.textContent = 'Subiendo...';

        const formData = new FormData();
        formData.append('image', file);

        fetch('components/blog/upload_image.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                featuredImage.value = data.url;
                coverPreviewImg.src = data.url;
                coverPreview.style.display = 'block';
                coverUploadStatus.textContent = '';
            } else {
                Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
                coverUploadStatus.textContent = '';
            }
            coverInput.value = '';
        })
        .catch(() => {
            Swal.fire({ title: 'Error de conexión', text: 'No se pudo conectar al servidor', icon: 'error', confirmButtonText: 'OK' });
            coverUploadStatus.textContent = '';
            coverInput.value = '';
        });
    });

    document.getElementById('btnRemoveCover').addEventListener('click', function() {
        featuredImage.value = '';
        coverPreviewImg.src = '';
        coverPreview.style.display = 'none';
    });

    const contentImageInput = document.getElementById('contentImageInput');
    const contentImageUploadStatus = document.getElementById('contentImageUploadStatus');

    document.getElementById('btnInsertContentImage').addEventListener('click', function() {
        contentImageInput.click();
    });

    contentImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        if (file.type !== 'image/png' || !file.name.toLowerCase().endsWith('.png')) {
            Swal.fire({ title: 'Error', text: 'Solo se permiten imágenes PNG', icon: 'error', confirmButtonText: 'OK' });
            contentImageInput.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({ title: 'Error', text: 'La imagen no debe superar los 2 MB', icon: 'error', confirmButtonText: 'OK' });
            contentImageInput.value = '';
            return;
        }

        contentImageUploadStatus.textContent = 'Subiendo...';

        const formData = new FormData();
        formData.append('image', file);

        fetch('components/blog/upload_image.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const cursor = easyMDE.codemirror.getCursor();
                easyMDE.codemirror.replaceRange(`\n![${file.name}](${data.url})\n`, cursor);
                contentImageUploadStatus.textContent = '';
            } else {
                Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
                contentImageUploadStatus.textContent = '';
            }
            contentImageInput.value = '';
        })
        .catch(() => {
            Swal.fire({ title: 'Error de conexión', text: 'No se pudo conectar al servidor', icon: 'error', confirmButtonText: 'OK' });
            contentImageUploadStatus.textContent = '';
            contentImageInput.value = '';
        });
    });

    document.getElementById('blogForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const title = document.getElementById('blogTitle').value.trim();
        const content = easyMDE.value().trim();

        if (!title || !content) {
            Swal.fire({ title: 'Campos obligatorios', text: 'El título y el contenido son requeridos', icon: 'warning', confirmButtonText: 'OK' });
            return;
        }

        const formData = new FormData(this);
        formData.set('content', content);

        const action = editingId ? 'update' : 'create';
        formData.append('action', action);

        fetch('components/blog/api_blog.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Éxito',
                    text: editingId ? 'Post actualizado correctamente' : 'Post creado correctamente',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                if (!editingId) {
                    resetForm();
                }
            } else {
                Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
            }
        })
        .catch(() => {
            Swal.fire({ title: 'Error de conexión', text: 'No se pudo conectar al servidor', icon: 'error', confirmButtonText: 'OK' });
        });
    });

    document.getElementById('btnNewPost').addEventListener('click', function() {
        resetForm();
    });

    document.getElementById('btnRefreshList').addEventListener('click', loadPosts);

    document.getElementById('gestionar-tab').addEventListener('shown.bs.tab', function() {
        loadPosts();
    });

    document.getElementById('redactar-tab').addEventListener('shown.bs.tab', function() {
        setTimeout(enforceEditorFullWidth, 100);
    });

    window.addEventListener('resize', function() {
        if (document.getElementById('redactar').classList.contains('active')) {
            enforceEditorFullWidth();
        }
    });

    function resetForm() {
        editingId = null;
        document.getElementById('blogForm').reset();
        document.getElementById('postId').value = '';
        document.getElementById('featuredImage').value = '';
        document.getElementById('blogStatus').value = 'draft';
        easyMDE.value('');
        coverPreview.style.display = 'none';
        coverPreviewImg.src = '';
        document.getElementById('contentImagesSection').style.display = 'none';
        document.getElementById('btnSavePost').innerHTML = '<i class="bi bi-check-lg"></i> Guardar';
        setTimeout(enforceEditorFullWidth, 50);
        document.getElementById('blogTitle').focus();
    }

    function loadPosts() {
        const container = document.getElementById('postsTableContainer');
        container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';

        const formData = new FormData();
        formData.append('action', 'list');

        fetch('components/blog/api_blog.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (!data.success || !data.data.length) {
                container.innerHTML = '<div class="text-center text-muted py-4">No hay posts registrados.</div>';
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-striped table-hover align-middle" style="width:100%;table-layout:fixed;">';
            html += '<thead class="table-dark"><tr>';
            html += '<th style="width:30%;">Título</th>';
            html += '<th style="width:15%;">Autor</th>';
            html += '<th style="width:12%;">Estado</th>';
            html += '<th style="width:20%;">Fecha</th>';
            html += '<th style="width:23%;">Acciones</th>';
            html += '</tr></thead><tbody>';

            data.data.forEach(post => {
                const badge = post.status === 'published'
                    ? '<span class="badge bg-success">Publicado</span>'
                    : '<span class="badge bg-warning text-dark">Borrador</span>';

                html += '<tr>';
                html += '<td><strong>' + escapeHtml(post.title) + '</strong></td>';
                html += '<td>' + escapeHtml(post.author) + '</td>';
                html += '<td>' + badge + '</td>';
                html += '<td><small>' + escapeHtml(post.created_at) + '</small></td>';
                html += '<td>';
                html += '<button class="btn btn-sm bg-indigo-dark text-white me-1" onclick="editPost(' + post.id + ')" title="Editar"><i class="bi bi-pencil"></i></button>';
                html += '<button class="btn btn-sm btn-outline-danger" onclick="deletePost(' + post.id + ')" title="Eliminar"><i class="bi bi-trash"></i></button>';
                html += '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
            container.innerHTML = html;
        })
        .catch(() => {
            container.innerHTML = '<div class="text-center text-danger py-4">Error al cargar los posts.</div>';
        });
    }
});

function extractImagesFromMarkdown(content) {
    const images = [];
    if (!content) return images;
    const mdRegex = /!\[.*?\]\(([^)]+)\)/g;
    let match;
    while ((match = mdRegex.exec(content)) !== null) {
        images.push(match[1]);
    }
    const htmlRegex = /<img[^>]+src=["']([^"']+)["'][^>]*>/gi;
    while ((match = htmlRegex.exec(content)) !== null) {
        images.push(match[1]);
    }
    return [...new Set(images)];
}

function removeImageFromContent(url) {
    var content = easyMDE.value();
    var escapedUrl = url.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    content = content.replace(new RegExp('!\\[.*?\\]\\(\\s*' + escapedUrl + '\\s*\\)', 'g'), '');
    content = content.replace(new RegExp('<img[^>]*src=["\']\\s*' + escapedUrl + '\\s*["\'][^>]*>', 'gi'), '');
    content = content.replace(/\n{3,}/g, '\n\n').trim();
    easyMDE.value(content);
    showContentImages(content);
}

var contentImagesDebounce = null;

function showContentImages(content) {
    const section = document.getElementById('contentImagesSection');
    const container = document.getElementById('contentImages');
    const images = extractImagesFromMarkdown(content);

    if (images.length === 0) {
        section.style.display = 'none';
        return;
    }

    section.style.display = 'block';
    container.innerHTML = images.map(function(url) {
        var escapedAttr = url.replace(/'/g, '\\x27').replace(/"/g, '&quot;');
        return '<div class="position-relative" style="width:120px;">' +
            '<button type="button" class="btn-close position-absolute top-0 end-0 bg-danger rounded-circle p-1 m-1" ' +
            'style="width:20px;height:20px;font-size:10px;opacity:0.9;z-index:1;" ' +
            'title="Eliminar del contenido" ' +
            'onclick="removeImageFromContent(\'' + escapedAttr + '\')"></button>' +
            '<a href="' + url + '" target="_blank">' +
            '<img src="' + url + '" style="width:120px;height:90px;object-fit:cover;" class="img-thumbnail" alt="">' +
            '</a>' +
            '</div>';
    }).join('');
}

function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function editPost(id) {
    const formData = new FormData();
    formData.append('action', 'get');
    formData.append('id', id);

    fetch('components/blog/api_blog.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
            return;
        }
        const post = data.data;
        editingId = post.id;
        document.getElementById('postId').value = post.id;
        document.getElementById('blogTitle').value = post.title;
        document.getElementById('blogStatus').value = post.status;
        document.getElementById('blogExcerpt').value = post.excerpt || '';
        document.getElementById('featuredImage').value = post.featured_image || '';
        easyMDE.value(post.content);
        document.getElementById('btnSavePost').innerHTML = '<i class="bi bi-check-lg"></i> Actualizar';

        if (post.featured_image) {
            document.getElementById('coverPreviewImg').src = post.featured_image;
            document.getElementById('coverPreview').style.display = 'block';
        } else {
            document.getElementById('coverPreview').style.display = 'none';
        }

        showContentImages(post.content);

        document.getElementById('redactar-tab').click();
        setTimeout(enforceEditorFullWidth, 100);
        document.getElementById('blogTitle').focus();
    })
    .catch(() => {
        Swal.fire({ title: 'Error de conexión', text: 'No se pudo conectar al servidor', icon: 'error', confirmButtonText: 'OK' });
    });
}

function deletePost(id) {
    Swal.fire({
        title: '¿Eliminar post?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
    }).then(function(result) {
        if (!result.isConfirmed) return;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        fetch('components/blog/api_blog.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ title: 'Eliminado', text: 'Post eliminado correctamente', icon: 'success', confirmButtonText: 'OK' });
                document.getElementById('btnRefreshList').click();
            } else {
                Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
            }
        })
        .catch(() => {
            Swal.fire({ title: 'Error de conexión', text: 'No se pudo conectar al servidor', icon: 'error', confirmButtonText: 'OK' });
        });
    });
}
</script>
