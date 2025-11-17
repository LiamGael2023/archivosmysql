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
    const days = prompt('¿Cuántos días será válido el enlace?\n\n0 = sin expiración\n7 = una semana\n30 = un mes', '7');
    if (days !== null) {
        submitForm(BASE_URL + '/share/create', {
            entity_type: 'file',
            entity_id: fileId,
            expires_days: days,
            folder_id: currentFolder || ''
        });
    }
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
    const days = prompt('¿Cuántos días será válido el enlace?\n\n0 = sin expiración\n7 = una semana\n30 = un mes', '7');
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

// Agregar inputs dinámicos para metakeys en el modal de subir
let metaKeyCounter = 1;
function addMetaInput() {
    const container = document.getElementById('metaKeysContainer');
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
        <div class="col">
            <input type="text" name="meta_keys[${metaKeyCounter}][key]" class="form-control" placeholder="Clave (ej: categoría)">
        </div>
        <div class="col">
            <input type="text" name="meta_keys[${metaKeyCounter}][value]" class="form-control" placeholder="Valor (ej: importante)">
        </div>
    `;
    container.appendChild(div);
    metaKeyCounter++;
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

console.log('Sistema de Gestión de Archivos cargado correctamente');
