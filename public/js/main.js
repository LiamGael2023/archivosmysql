// Funciones para modales
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal.classList.contains('active')) {
        modal.classList.remove('active');
    } else {
        modal.classList.add('active');
    }
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}

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
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = BASE_URL + '/file/rename';

        const fileIdInput = document.createElement('input');
        fileIdInput.type = 'hidden';
        fileIdInput.name = 'file_id';
        fileIdInput.value = fileId;

        const nameInput = document.createElement('input');
        nameInput.type = 'hidden';
        nameInput.name = 'name';
        nameInput.value = newName;

        const folderInput = document.createElement('input');
        folderInput.type = 'hidden';
        folderInput.name = 'folder_id';
        folderInput.value = currentFolder || '';

        form.appendChild(fileIdInput);
        form.appendChild(nameInput);
        form.appendChild(folderInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteFile(fileId) {
    if (confirm('¿Estás seguro de eliminar este archivo?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = BASE_URL + '/file/delete';

        const fileIdInput = document.createElement('input');
        fileIdInput.type = 'hidden';
        fileIdInput.name = 'file_id';
        fileIdInput.value = fileId;

        const folderInput = document.createElement('input');
        folderInput.type = 'hidden';
        folderInput.name = 'folder_id';
        folderInput.value = currentFolder || '';

        form.appendChild(fileIdInput);
        form.appendChild(folderInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function shareFile(fileId) {
    const days = prompt('¿Cuántos días será válido el enlace? (0 = sin expiración):', '7');
    if (days !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = BASE_URL + '/share/create';

        const entityType = document.createElement('input');
        entityType.type = 'hidden';
        entityType.name = 'entity_type';
        entityType.value = 'file';

        const entityId = document.createElement('input');
        entityId.type = 'hidden';
        entityId.name = 'entity_id';
        entityId.value = fileId;

        const expiresDays = document.createElement('input');
        expiresDays.type = 'hidden';
        expiresDays.name = 'expires_days';
        expiresDays.value = days;

        const folderInput = document.createElement('input');
        folderInput.type = 'hidden';
        folderInput.name = 'folder_id';
        folderInput.value = currentFolder || '';

        form.appendChild(entityType);
        form.appendChild(entityId);
        form.appendChild(expiresDays);
        form.appendChild(folderInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Funciones de carpetas
function renameFolder(folderId, currentName) {
    const newName = prompt('Nuevo nombre de la carpeta:', currentName);
    if (newName && newName !== currentName) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = BASE_URL + '/folder/rename';

        const folderIdInput = document.createElement('input');
        folderIdInput.type = 'hidden';
        folderIdInput.name = 'folder_id';
        folderIdInput.value = folderId;

        const nameInput = document.createElement('input');
        nameInput.type = 'hidden';
        nameInput.name = 'name';
        nameInput.value = newName;

        const parentInput = document.createElement('input');
        parentInput.type = 'hidden';
        parentInput.name = 'parent_id';
        parentInput.value = currentFolder || '';

        form.appendChild(folderIdInput);
        form.appendChild(nameInput);
        form.appendChild(parentInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteFolder(folderId) {
    if (confirm('¿Estás seguro de eliminar esta carpeta? Se eliminarán todos sus contenidos.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = BASE_URL + '/folder/delete';

        const folderIdInput = document.createElement('input');
        folderIdInput.type = 'hidden';
        folderIdInput.name = 'folder_id';
        folderIdInput.value = folderId;

        const parentInput = document.createElement('input');
        parentInput.type = 'hidden';
        parentInput.name = 'parent_id';
        parentInput.value = currentFolder || '';

        form.appendChild(folderIdInput);
        form.appendChild(parentInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function shareFolder(folderId) {
    const days = prompt('¿Cuántos días será válido el enlace? (0 = sin expiración):', '7');
    if (days !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = BASE_URL + '/share/create';

        const entityType = document.createElement('input');
        entityType.type = 'hidden';
        entityType.name = 'entity_type';
        entityType.value = 'folder';

        const entityId = document.createElement('input');
        entityId.type = 'hidden';
        entityId.name = 'entity_id';
        entityId.value = folderId;

        const expiresDays = document.createElement('input');
        expiresDays.type = 'hidden';
        expiresDays.name = 'expires_days';
        expiresDays.value = days;

        const folderInput = document.createElement('input');
        folderInput.type = 'hidden';
        folderInput.name = 'folder_id';
        folderInput.value = currentFolder || '';

        form.appendChild(entityType);
        form.appendChild(entityId);
        form.appendChild(expiresDays);
        form.appendChild(folderInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Funciones de metakeys
function addMeta(fileId) {
    const metaKey = prompt('Nombre del metakey:');
    if (metaKey) {
        const metaValue = prompt('Valor del metakey (opcional):');

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = BASE_URL + '/file/add-meta';

        const fileIdInput = document.createElement('input');
        fileIdInput.type = 'hidden';
        fileIdInput.name = 'file_id';
        fileIdInput.value = fileId;

        const keyInput = document.createElement('input');
        keyInput.type = 'hidden';
        keyInput.name = 'meta_key';
        keyInput.value = metaKey;

        const valueInput = document.createElement('input');
        valueInput.type = 'hidden';
        valueInput.name = 'meta_value';
        valueInput.value = metaValue || '';

        const folderInput = document.createElement('input');
        folderInput.type = 'hidden';
        folderInput.name = 'folder_id';
        folderInput.value = currentFolder || '';

        form.appendChild(fileIdInput);
        form.appendChild(keyInput);
        form.appendChild(valueInput);
        form.appendChild(folderInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteMeta(metaId) {
    if (confirm('¿Eliminar este metakey?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = BASE_URL + '/file/delete-meta';

        const metaIdInput = document.createElement('input');
        metaIdInput.type = 'hidden';
        metaIdInput.name = 'meta_id';
        metaIdInput.value = metaId;

        const folderInput = document.createElement('input');
        folderInput.type = 'hidden';
        folderInput.name = 'folder_id';
        folderInput.value = currentFolder || '';

        form.appendChild(metaIdInput);
        form.appendChild(folderInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Agregar inputs dinámicos para metakeys en el modal de subir
let metaKeyCounter = 1;
function addMetaInput() {
    const container = document.getElementById('metaKeysContainer');
    const div = document.createElement('div');
    div.className = 'meta-input';
    div.innerHTML = `
        <input type="text" name="meta_keys[${metaKeyCounter}][key]" placeholder="Clave">
        <input type="text" name="meta_keys[${metaKeyCounter}][value]" placeholder="Valor">
    `;
    container.appendChild(div);
    metaKeyCounter++;
}
