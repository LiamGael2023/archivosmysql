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
     * Subir archivo(s)
     */
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        if (!isset($_FILES['files'])) {
            $_SESSION['error'] = 'No se seleccionaron archivos';
            header('Location: ' . BASE_URL);
            exit;
        }

        $folder_id = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' ? (int)$_POST['folder_id'] : null;

        // Convertir 0 a null
        if ($folder_id === 0) {
            $folder_id = null;
        }

        $files = $_FILES['files'];
        $uploadedCount = 0;
        $errorCount = 0;
        $errors = [];

        // Procesar cada archivo
        for ($i = 0; $i < count($files['name']); $i++) {
            // Verificar si hubo error en la subida
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $errorCount++;
                $errors[] = $files['name'][$i] . ': Error al subir';
                continue;
            }

            // Validar tamaño
            if ($files['size'][$i] > MAX_UPLOAD_SIZE) {
                $errorCount++;
                $errors[] = $files['name'][$i] . ': Excede el tamaño máximo permitido';
                continue;
            }

            // Generar nombre único
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '_' . $i . '.' . $extension;
            $filepath = UPLOAD_DIR . $filename;

            // Mover archivo
            if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                $data = [
                    'name' => pathinfo($files['name'][$i], PATHINFO_FILENAME),
                    'original_name' => $files['name'][$i],
                    'file_path' => $filepath,
                    'extension' => $extension,
                    'size' => $files['size'][$i],
                    'mime_type' => $files['type'][$i],
                    'folder_id' => $folder_id
                ];

                $file_id = $this->fileModel->create($data);

                // Agregar metakeys individuales para este archivo si existen
                if (isset($_POST['file_meta'][$i]) && is_array($_POST['file_meta'][$i])) {
                    foreach ($_POST['file_meta'][$i] as $meta) {
                        if (!empty($meta['key'])) {
                            $this->metaKeyModel->add('file', $file_id, $meta['key'], $meta['value'] ?? null);
                        }
                    }
                }

                $uploadedCount++;
            } else {
                $errorCount++;
                $errors[] = $files['name'][$i] . ': Error al guardar';
            }
        }

        // Establecer mensaje de resultado
        if ($uploadedCount > 0 && $errorCount === 0) {
            $_SESSION['success'] = $uploadedCount === 1
                ? 'Archivo subido correctamente'
                : $uploadedCount . ' archivos subidos correctamente';
        } elseif ($uploadedCount > 0 && $errorCount > 0) {
            $_SESSION['success'] = $uploadedCount . ' archivo(s) subido(s). ' . $errorCount . ' error(es)';
            if (!empty($errors)) {
                $_SESSION['error'] = implode(', ', $errors);
            }
        } else {
            $_SESSION['error'] = 'Error al subir los archivos';
            if (!empty($errors)) {
                $_SESSION['error'] .= ': ' . implode(', ', $errors);
            }
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
        $folder_id = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' ? (int)$_POST['folder_id'] : null;

        // Convertir 0 a null
        if ($folder_id === 0) {
            $folder_id = null;
        }

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
        $folder_id = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' ? (int)$_POST['folder_id'] : null;

        // Convertir 0 a null
        if ($folder_id === 0) {
            $folder_id = null;
        }

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
        $folder_id = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' ? (int)$_POST['folder_id'] : null;

        // Convertir 0 a null
        if ($folder_id === 0) {
            $folder_id = null;
        }

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
        $folder_id = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' ? (int)$_POST['folder_id'] : null;

        // Convertir 0 a null
        if ($folder_id === 0) {
            $folder_id = null;
        }

        if ($meta_id) {
            $this->metaKeyModel->delete($meta_id);
            $_SESSION['success'] = 'Metakey eliminado correctamente';
        }

        header('Location: ' . BASE_URL . ($folder_id ? '?folder=' . $folder_id : ''));
        exit;
    }
}
