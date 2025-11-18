/**
 * JavaScript principal para el Sistema de Gestión de Archivos
 * Compatible con Tabler.io
 */

// Funciones de ordenamiento
function updateOrder(orderBy) {
    const url = new URL(window.location.href);
    url.searchParams.set('order', orderBy);
    window.location.href = url.toString();
}

function updateDirection(dir) {
    const url = new URL(window.location.href);
    url.searchParams.set('dir', dir);
    window.location.href = url.toString();
}

// Funciones de archivos
function renameFile(fileId, currentName) {
    const newName = prompt('Nuevo nombre del archivo:', currentName);
    if (newName && newName !== currentName) {
        submitForm(BASE_URL + '/file/rename', {
            file_id: fileId,
            name: newName,
            folder_id: currentFolder || ''
        });
    }
}

function deleteFile(fileId) {
    if (confirm('¿Estás seguro de eliminar este archivo?\n\nEsta acción no se puede deshacer.')) {
        submitForm(BASE_URL + '/file/delete', {
            file_id: fileId,
            folder_id: currentFolder || ''
        });
    }
}

function shareFile(fileId) {
    const days = prompt('¿Cuántos días será válido el enlace?\n\n0 = sin expiración (indefinido)\n7 = una semana\n30 = un mes', '0');
    if (days !== null) {
        submitForm(BASE_URL + '/share/create', {
            entity_type: 'file',
            entity_id: fileId,
            expires_days: days,
            folder_id: currentFolder || ''
        });
    }
}

// Previsualizar archivo
function previewFile(fileId, fileName, extension) {
    const modal = new bootstrap.Modal(document.getElementById('previewFileModal'));
    const contentContainer = document.getElementById('previewFileContent');
    const fileNameSpan = document.getElementById('previewFileName');
    const downloadBtn = document.getElementById('previewDownloadBtn');

    // Actualizar título y botón de descarga
    fileNameSpan.textContent = fileName;
    downloadBtn.href = BASE_URL + '/file/download?id=' + fileId;

    // Extensiones de imagen
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
    // Extensiones de PDF
    const pdfExtensions = ['pdf'];
    // Extensiones de texto/código
    const textExtensions = ['txt', 'html', 'css', 'js', 'json', 'xml', 'md', 'php', 'py', 'java', 'c', 'cpp', 'h'];
    // Extensiones de video
    const videoExtensions = ['mp4', 'webm', 'ogg'];
    // Extensiones de audio
    const audioExtensions = ['mp3', 'wav', 'ogg', 'flac'];

    const previewUrl = BASE_URL + '/file/preview?id=' + fileId;

    // Generar contenido según el tipo de archivo
    if (imageExtensions.includes(extension)) {
        contentContainer.innerHTML = `
            <div class="text-center p-4">
                <img src="${previewUrl}" alt="${fileName}" class="img-fluid" style="max-height: 70vh;">
            </div>
        `;
    } else if (pdfExtensions.includes(extension)) {
        contentContainer.innerHTML = `
            <div style="height: 70vh;">
                <iframe src="${previewUrl}" width="100%" height="100%" style="border: none;"></iframe>
            </div>
        `;
    } else if (videoExtensions.includes(extension)) {
        contentContainer.innerHTML = `
            <div class="text-center p-4">
                <video controls class="w-100" style="max-height: 70vh;">
                    <source src="${previewUrl}" type="video/${extension}">
                    Tu navegador no soporta la reproducción de video.
                </video>
            </div>
        `;
    } else if (audioExtensions.includes(extension)) {
        contentContainer.innerHTML = `
            <div class="text-center p-4">
                <div class="mb-3">
                    <i class="ti ti-music icon" style="font-size: 4rem; color: #206bc4;"></i>
                </div>
                <h4>${fileName}</h4>
                <audio controls class="w-100 mt-3">
                    <source src="${previewUrl}" type="audio/${extension === 'mp3' ? 'mpeg' : extension}">
                    Tu navegador no soporta la reproducción de audio.
                </audio>
            </div>
        `;
    } else if (textExtensions.includes(extension)) {
        // Para archivos de texto, cargar y mostrar el contenido
        fetch(previewUrl)
            .then(response => response.text())
            .then(text => {
                contentContainer.innerHTML = `
                    <div class="p-3" style="max-height: 70vh; overflow-y: auto;">
                        <pre class="mb-0" style="white-space: pre-wrap; word-wrap: break-word;"><code>${escapeHtml(text)}</code></pre>
                    </div>
                `;
            })
            .catch(error => {
                contentContainer.innerHTML = `
                    <div class="text-center p-4">
                        <div class="empty">
                            <div class="empty-icon text-danger">
                                <i class="ti ti-alert-circle icon" style="font-size: 3rem;"></i>
                            </div>
                            <p class="empty-title">Error al cargar el archivo</p>
                        </div>
                    </div>
                `;
            });
    } else {
        // Archivo no previsualizable
        const fileIcons = {
            'doc': 'ti-file-type-doc',
            'docx': 'ti-file-type-docx',
            'xls': 'ti-file-type-xls',
            'xlsx': 'ti-file-type-xls',
            'ppt': 'ti-file-type-ppt',
            'pptx': 'ti-file-type-ppt',
            'zip': 'ti-file-zip',
            'rar': 'ti-file-zip',
            '7z': 'ti-file-zip',
            'exe': 'ti-app-window',
            'default': 'ti-file'
        };

        const iconClass = fileIcons[extension] || fileIcons['default'];

        contentContainer.innerHTML = `
            <div class="text-center p-4">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="${iconClass} icon" style="font-size: 4rem; color: #6c757d;"></i>
                    </div>
                    <p class="empty-title">${fileName}</p>
                    <p class="empty-subtitle text-muted">
                        Este tipo de archivo (.${extension.toUpperCase()}) no se puede previsualizar directamente.
                    </p>
                    <div class="empty-action">
                        <a href="${BASE_URL}/file/download?id=${fileId}" class="btn btn-primary">
                            <i class="ti ti-download icon"></i> Descargar archivo
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    modal.show();
}

// Funciones de carpetas
function renameFolder(folderId, currentName) {
    const newName = prompt('Nuevo nombre de la carpeta:', currentName);
    if (newName && newName !== currentName) {
        submitForm(BASE_URL + '/folder/rename', {
            folder_id: folderId,
            name: newName,
            parent_id: currentFolder || ''
        });
    }
}

function deleteFolder(folderId) {
    if (confirm('¿Estás seguro de eliminar esta carpeta?\n\nSe eliminarán todos sus contenidos (subcarpetas y archivos).\nEsta acción no se puede deshacer.')) {
        submitForm(BASE_URL + '/folder/delete', {
            folder_id: folderId,
            parent_id: currentFolder || ''
        });
    }
}

function shareFolder(folderId) {
    const days = prompt('¿Cuántos días será válido el enlace?\n\n0 = sin expiración (indefinido)\n7 = una semana\n30 = un mes', '0');
    if (days !== null) {
        submitForm(BASE_URL + '/share/create', {
            entity_type: 'folder',
            entity_id: folderId,
            expires_days: days,
            folder_id: currentFolder || ''
        });
    }
}

// Funciones de metakeys
function addMeta(fileId) {
    const metaKey = prompt('Nombre del metakey:\n\nEjemplos: categoría, proyecto, estado, prioridad');
    if (metaKey) {
        const metaValue = prompt('Valor del metakey (opcional):\n\nEjemplo: Si el metakey es "categoría", el valor puede ser "importante"');
        submitForm(BASE_URL + '/file/add-meta', {
            file_id: fileId,
            meta_key: metaKey,
            meta_value: metaValue || '',
            folder_id: currentFolder || ''
        });
    }
}

function deleteMeta(metaId) {
    if (confirm('¿Eliminar este metakey?')) {
        submitForm(BASE_URL + '/file/delete-meta', {
            meta_id: metaId,
            folder_id: currentFolder || ''
        });
    }
}

// Manejar selección de archivos y generar campos de metakeys individuales
function handleFileSelect(input) {
    const files = input.files;
    const container = document.getElementById('filesMetaContainer');

    // Limpiar contenedor
    container.innerHTML = '';

    if (files.length === 0) {
        return;
    }

    // Crear una tarjeta para cada archivo
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fileSize = formatFileSize(file.size);

        const fileCard = document.createElement('div');
        fileCard.className = 'card mb-3';
        fileCard.innerHTML = `
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <i class="ti ti-file-text icon" style="font-size: 2rem; color: #206bc4;"></i>
                    </div>
                    <div class="flex-fill">
                        <h4 class="card-title mb-1">${escapeHtml(file.name)}</h4>
                        <p class="text-muted mb-0">Tamaño: ${fileSize}</p>
                    </div>
                </div>

                <div class="metakeys-section">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Metakeys para este archivo (opcional)</label>
                        <button type="button" class="btn btn-sm btn-ghost-success" onclick="addMetaKeyForFile(${i})">
                            <i class="ti ti-plus icon"></i> Agregar Metakey
                        </button>
                    </div>
                    <div id="metaKeysFile${i}" class="metakeys-container">
                        <div class="row mb-2">
                            <div class="col-md-5">
                                <input type="text" name="file_meta[${i}][0][key]" class="form-control" placeholder="Clave (ej: categoría)">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="file_meta[${i}][0][value]" class="form-control" placeholder="Valor (ej: importante)">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-ghost-danger w-100" onclick="this.closest('.row').remove()">
                                    <i class="ti ti-trash icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(fileCard);
    }

    // Mostrar mensaje con cantidad de archivos
    if (files.length > 1) {
        const countMsg = document.createElement('div');
        countMsg.className = 'alert alert-info mb-3';
        countMsg.innerHTML = `
            <i class="ti ti-info-circle icon me-2"></i>
            Has seleccionado <strong>${files.length} archivos</strong>. Puedes agregar metakeys individuales para cada uno.
        `;
        container.insertBefore(countMsg, container.firstChild);
    }
}

// Agregar metakey adicional para un archivo específico
function addMetaKeyForFile(fileIndex) {
    const container = document.getElementById(`metaKeysFile${fileIndex}`);
    const metaCount = container.querySelectorAll('.row').length;

    const newRow = document.createElement('div');
    newRow.className = 'row mb-2';
    newRow.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="file_meta[${fileIndex}][${metaCount}][key]" class="form-control" placeholder="Clave (ej: categoría)">
        </div>
        <div class="col-md-5">
            <input type="text" name="file_meta[${fileIndex}][${metaCount}][value]" class="form-control" placeholder="Valor (ej: importante)">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-ghost-danger w-100" onclick="this.closest('.row').remove()">
                <i class="ti ti-trash icon"></i>
            </button>
        </div>
    `;

    container.appendChild(newRow);
}

// Función auxiliar para escapar HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Función auxiliar para formatear tamaño de archivo
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Función auxiliar para enviar formularios
function submitForm(action, data) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = data[key];
            form.appendChild(input);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

// Copiar al portapapeles
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('URL copiada al portapapeles', 'success');
        }, function(err) {
            console.error('Error al copiar: ', err);
            showNotification('Error al copiar la URL', 'danger');
        });
    } else {
        // Fallback para navegadores antiguos
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showNotification('URL copiada al portapapeles', 'success');
        } catch (err) {
            console.error('Error al copiar: ', err);
            showNotification('Error al copiar la URL', 'danger');
        }
        document.body.removeChild(textArea);
    }
}

// Mostrar notificaciones
function showNotification(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible position-fixed top-0 end-0 m-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
        <div class="d-flex">
            <div>
                <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'} icon alert-icon"></i>
            </div>
            <div>${message}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    `;
    document.body.appendChild(alertDiv);

    // Auto-dismiss después de 3 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Auto-dismiss de alertas existentes
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Mejorar búsqueda en móviles
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
});

// Confirmar antes de salir si hay cambios sin guardar en modales
let formChanged = false;
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal form');
    modals.forEach(function(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(function(input) {
            input.addEventListener('change', function() {
                formChanged = true;
            });
        });

        form.addEventListener('submit', function() {
            formChanged = false;
        });
    });

    // Resetear flag cuando se cierra el modal
    const modalElements = document.querySelectorAll('.modal');
    modalElements.forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            if (formChanged) {
                formChanged = false;
            }
        });
    });
});

// Prevenir doble submit
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
            }
        });
    });
});

// Drag and drop para subir archivos (mejora futura)
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) {
        const dropZone = fileInput.closest('.modal-body');
        if (dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropZone.classList.add('bg-light');
            }

            function unhighlight(e) {
                dropZone.classList.remove('bg-light');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
            }
        }
    }
});

// Resetear modal de subida de archivos al cerrarlo
document.addEventListener('DOMContentLoaded', function() {
    const uploadModal = document.getElementById('uploadFileModal');
    if (uploadModal) {
        uploadModal.addEventListener('hidden.bs.modal', function () {
            const fileInput = document.getElementById('fileInput');
            const filesMetaContainer = document.getElementById('filesMetaContainer');

            if (fileInput) {
                fileInput.value = '';
            }
            if (filesMetaContainer) {
                filesMetaContainer.innerHTML = '';
            }
        });
    }
});

// Función para expandir/colapsar carpetas en el árbol
function toggleTreeFolder(button) {
    const treeItem = button.closest('.tree-item');
    const children = treeItem.querySelector('.tree-children');
    const icon = button.querySelector('i');

    if (children) {
        treeItem.classList.toggle('collapsed');

        if (treeItem.classList.contains('collapsed')) {
            icon.className = 'ti ti-chevron-right';
        } else {
            icon.className = 'ti ti-chevron-down';
        }
    }
}

// Expandir automáticamente la ruta activa al cargar
document.addEventListener('DOMContentLoaded', function() {
    const activeLink = document.querySelector('.folder-tree .tree-link.active');
    if (activeLink) {
        let parent = activeLink.closest('.tree-children');
        while (parent) {
            const parentItem = parent.closest('.tree-item');
            if (parentItem) {
                parentItem.classList.remove('collapsed');
                const icon = parentItem.querySelector('.tree-toggle i');
                if (icon) {
                    icon.className = 'ti ti-chevron-down';
                }
            }
            parent = parentItem ? parentItem.parentElement.closest('.tree-children') : null;
        }
    }
});

console.log('Sistema de Gestión de Archivos cargado correctamente');
