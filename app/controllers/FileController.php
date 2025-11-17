<?php
/**
 * Controlador de Archivos
 */
class FileController {
    private $fileModel;
    private $metaKeyModel;

    public function __construct() {
        $this->fileModel = new File();
        $this->metaKeyModel = new MetaKey();
    }

    /**
     * Subir archivo
     */
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Error al subir el archivo';
            header('Location: ' . BASE_URL);
            exit;
        }

        $file = $_FILES['file'];
        $folder_id = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : null;

        // Validar tamaño
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            $_SESSION['error'] = 'El archivo excede el tamaño máximo permitido';
            header('Location: ' . BASE_URL . ($folder_id ? '?folder=' . $folder_id : ''));
            exit;
        }

        // Generar nombre único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = UPLOAD_DIR . $filename;

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $data = [
                'name' => pathinfo($file['name'], PATHINFO_FILENAME),
                'original_name' => $file['name'],
                'file_path' => $filepath,
                'extension' => $extension,
                'size' => $file['size'],
                'mime_type' => $file['type'],
                'folder_id' => $folder_id
            ];

            $file_id = $this->fileModel->create($data);

            // Agregar metakeys si existen
            if (isset($_POST['meta_keys']) && is_array($_POST['meta_keys'])) {
                foreach ($_POST['meta_keys'] as $meta) {
                    if (!empty($meta['key'])) {
                        $this->metaKeyModel->add('file', $file_id, $meta['key'], $meta['value'] ?? null);
                    }
                }
            }

            $_SESSION['success'] = 'Archivo subido correctamente';
        } else {
            $_SESSION['error'] = 'Error al guardar el archivo';
        }

        header('Location: ' . BASE_URL . ($folder_id ? '?folder=' . $folder_id : ''));
        exit;
    }

    /**
     * Renombrar archivo
     */
    public function rename() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $id = isset($_POST['file_id']) ? (int)$_POST['file_id'] : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $folder_id = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : null;

        if ($id && $name) {
            if ($this->fileModel->updateName($id, $name)) {
                $_SESSION['success'] = 'Archivo renombrado correctamente';
            } else {
                $_SESSION['error'] = 'Error al renombrar el archivo';
            }
        }

        header('Location: ' . BASE_URL . ($folder_id ? '?folder=' . $folder_id : ''));
        exit;
    }

    /**
     * Eliminar archivo
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $id = isset($_POST['file_id']) ? (int)$_POST['file_id'] : 0;
        $folder_id = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : null;

        if ($id) {
            if ($this->fileModel->delete($id)) {
                $_SESSION['success'] = 'Archivo eliminado correctamente';
            } else {
                $_SESSION['error'] = 'Error al eliminar el archivo';
            }
        }

        header('Location: ' . BASE_URL . ($folder_id ? '?folder=' . $folder_id : ''));
        exit;
    }

    /**
     * Descargar archivo
     */
    public function download() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$id) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $file = $this->fileModel->getById($id);

        if (!$file || !file_exists($file['file_path'])) {
            $_SESSION['error'] = 'Archivo no encontrado';
            header('Location: ' . BASE_URL);
            exit;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $file['mime_type']);
        header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
        header('Content-Length: ' . $file['size']);
        header('Pragma: public');

        readfile($file['file_path']);
        exit;
    }

    /**
     * Agregar metakey a archivo
     */
    public function addMetaKey() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $file_id = isset($_POST['file_id']) ? (int)$_POST['file_id'] : 0;
        $meta_key = isset($_POST['meta_key']) ? trim($_POST['meta_key']) : '';
        $meta_value = isset($_POST['meta_value']) ? trim($_POST['meta_value']) : null;
        $folder_id = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : null;

        if ($file_id && $meta_key) {
            $this->metaKeyModel->add('file', $file_id, $meta_key, $meta_value);
            $_SESSION['success'] = 'Metakey agregado correctamente';
        }

        header('Location: ' . BASE_URL . ($folder_id ? '?folder=' . $folder_id : ''));
        exit;
    }

    /**
     * Eliminar metakey
     */
    public function deleteMetaKey() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $meta_id = isset($_POST['meta_id']) ? (int)$_POST['meta_id'] : 0;
        $folder_id = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : null;

        if ($meta_id) {
            $this->metaKeyModel->delete($meta_id);
            $_SESSION['success'] = 'Metakey eliminado correctamente';
        }

        header('Location: ' . BASE_URL . ($folder_id ? '?folder=' . $folder_id : ''));
        exit;
    }
}
