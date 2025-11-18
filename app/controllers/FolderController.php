<?php
/**
 * Controlador de Carpetas
 */
class FolderController {
    private $folderModel;

    public function __construct() {
        $this->folderModel = new Folder();
    }

    /**
     * Crear carpeta
     */
    public function create() {
        AuthController::requirePermission('can_upload');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;

        // Convertir 0 a null para carpetas en la raíz
        if ($parent_id === 0) {
            $parent_id = null;
        }

        if ($name) {
            $this->folderModel->create($name, $parent_id);
            $_SESSION['success'] = 'Carpeta creada correctamente';
        } else {
            $_SESSION['error'] = 'El nombre de la carpeta es obligatorio';
        }

        header('Location: ' . BASE_URL . ($parent_id ? '?folder=' . $parent_id : ''));
        exit;
    }

    /**
     * Renombrar carpeta
     */
    public function rename() {
        AuthController::requirePermission('can_edit');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $id = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;

        // Convertir 0 a null
        if ($parent_id === 0) {
            $parent_id = null;
        }

        if ($id && $name) {
            if ($this->folderModel->updateName($id, $name)) {
                $_SESSION['success'] = 'Carpeta renombrada correctamente';
            } else {
                $_SESSION['error'] = 'Error al renombrar la carpeta';
            }
        }

        header('Location: ' . BASE_URL . ($parent_id ? '?folder=' . $parent_id : ''));
        exit;
    }

    /**
     * Eliminar carpeta
     */
    public function delete() {
        AuthController::requirePermission('can_delete');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $id = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : 0;
        $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;

        // Convertir 0 a null
        if ($parent_id === 0) {
            $parent_id = null;
        }

        if ($id && $id != 1) { // No permitir eliminar la carpeta raíz
            if ($this->folderModel->delete($id)) {
                $_SESSION['success'] = 'Carpeta eliminada correctamente';
            } else {
                $_SESSION['error'] = 'Error al eliminar la carpeta';
            }
        }

        header('Location: ' . BASE_URL . ($parent_id ? '?folder=' . $parent_id : ''));
        exit;
    }
}
