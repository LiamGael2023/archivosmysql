<?php
/**
 * Controlador de Enlaces Compartidos
 */
class ShareController {
    private $shareModel;
    private $fileModel;
    private $folderModel;

    public function __construct() {
        $this->shareModel = new SharedLink();
        $this->fileModel = new File();
        $this->folderModel = new Folder();
    }

    /**
     * Crear enlace compartido
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $entity_type = isset($_POST['entity_type']) ? $_POST['entity_type'] : '';
        $entity_id = isset($_POST['entity_id']) ? (int)$_POST['entity_id'] : 0;
        $expires_days = isset($_POST['expires_days']) ? (int)$_POST['expires_days'] : 0;
        $folder_id = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : null;

        $expires_at = null;
        if ($expires_days > 0) {
            $expires_at = date('Y-m-d H:i:s', strtotime("+{$expires_days} days"));
        }

        if (in_array($entity_type, ['file', 'folder']) && $entity_id) {
            $token = $this->shareModel->create($entity_type, $entity_id, $expires_at);
            $share_url = BASE_URL . '/share?token=' . $token;
            $_SESSION['success'] = 'Enlace creado: <a href="' . $share_url . '" target="_blank">' . $share_url . '</a>';
        } else {
            $_SESSION['error'] = 'Datos inválidos';
        }

        header('Location: ' . BASE_URL . ($folder_id ? '?folder=' . $folder_id : ''));
        exit;
    }

    /**
     * Ver contenido compartido
     */
    public function view() {
        $token = isset($_GET['token']) ? $_GET['token'] : '';

        if (!$token) {
            die('Token inválido');
        }

        if (!$this->shareModel->isValid($token)) {
            die('El enlace ha expirado o no es válido');
        }

        $shareDetails = $this->shareModel->getDetailsByToken($token);

        if (!$shareDetails) {
            die('Recurso no encontrado');
        }

        if ($shareDetails['entity_type'] === 'file') {
            // Descargar archivo
            if (!file_exists($shareDetails['file_path'])) {
                die('Archivo no encontrado');
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $shareDetails['original_name'] . '"');
            header('Content-Length: ' . filesize($shareDetails['file_path']));
            header('Pragma: public');

            readfile($shareDetails['file_path']);
            exit;
        } else {
            // Mostrar contenido de carpeta
            $folder_id = $shareDetails['entity_id'];
            $files = $this->fileModel->getByFolder($folder_id);
            $folders = $this->folderModel->getByParent($folder_id);

            require_once __DIR__ . '/../views/shared_folder.php';
        }
    }

    /**
     * Eliminar enlace compartido
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $share_id = isset($_POST['share_id']) ? (int)$_POST['share_id'] : 0;
        $folder_id = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : null;

        if ($share_id) {
            $this->shareModel->delete($share_id);
            $_SESSION['success'] = 'Enlace eliminado correctamente';
        }

        header('Location: ' . BASE_URL . ($folder_id ? '?folder=' . $folder_id : ''));
        exit;
    }

    /**
     * Listar enlaces de una entidad
     */
    public function listLinks() {
        $entity_type = isset($_GET['entity_type']) ? $_GET['entity_type'] : '';
        $entity_id = isset($_GET['entity_id']) ? (int)$_GET['entity_id'] : 0;

        if (!in_array($entity_type, ['file', 'folder']) || !$entity_id) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $links = $this->shareModel->getByEntity($entity_type, $entity_id);

        require_once __DIR__ . '/../views/shared_links.php';
    }
}
