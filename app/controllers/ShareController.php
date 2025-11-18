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
        AuthController::requirePermission('can_share');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $entity_type = isset($_POST['entity_type']) ? $_POST['entity_type'] : '';
        $entity_id = isset($_POST['entity_id']) ? (int)$_POST['entity_id'] : 0;
        $expires_days = isset($_POST['expires_days']) ? (int)$_POST['expires_days'] : 0;
        $folder_id = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' ? (int)$_POST['folder_id'] : null;

        // Convertir 0 a null
        if ($folder_id === 0) {
            $folder_id = null;
        }

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
            // Mostrar vista de archivo compartido
            $file = $this->fileModel->getById($shareDetails['entity_id']);
            if (!$file || !file_exists($file['file_path'])) {
                die('Archivo no encontrado');
            }

            $files = [$file];
            $folders = [];

            require_once __DIR__ . '/../views/shared_folder.php';
        } else {
            // Mostrar contenido de carpeta
            $folder_id = $shareDetails['entity_id'];
            $files = $this->fileModel->getByFolder($folder_id);
            $folders = $this->folderModel->getByParent($folder_id);

            require_once __DIR__ . '/../views/shared_folder.php';
        }
    }

    /**
     * Descargar archivo compartido
     */
    public function download() {
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        $file_id = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;

        if (!$token || !$file_id) {
            die('Parámetros inválidos');
        }

        if (!$this->shareModel->isValid($token)) {
            die('El enlace ha expirado o no es válido');
        }

        $shareDetails = $this->shareModel->getDetailsByToken($token);

        if (!$shareDetails) {
            die('Recurso no encontrado');
        }

        // Verificar que el archivo pertenezca al recurso compartido
        $file = $this->fileModel->getById($file_id);

        if (!$file) {
            die('Archivo no encontrado');
        }

        // Si es archivo compartido directamente
        if ($shareDetails['entity_type'] === 'file' && $shareDetails['entity_id'] != $file_id) {
            die('Acceso no autorizado');
        }

        // Si es carpeta compartida, verificar que el archivo esté en esa carpeta
        if ($shareDetails['entity_type'] === 'folder' && $file['folder_id'] != $shareDetails['entity_id']) {
            die('Acceso no autorizado');
        }

        if (!file_exists($file['file_path'])) {
            die('Archivo no encontrado en el servidor');
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
     * Previsualizar archivo compartido
     */
    public function preview() {
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        $file_id = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;

        if (!$token || !$file_id) {
            header('HTTP/1.0 400 Bad Request');
            exit;
        }

        if (!$this->shareModel->isValid($token)) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }

        $shareDetails = $this->shareModel->getDetailsByToken($token);

        if (!$shareDetails) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        $file = $this->fileModel->getById($file_id);

        if (!$file) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        // Verificar acceso
        if ($shareDetails['entity_type'] === 'file' && $shareDetails['entity_id'] != $file_id) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }

        if ($shareDetails['entity_type'] === 'folder' && $file['folder_id'] != $shareDetails['entity_id']) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }

        if (!file_exists($file['file_path'])) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        header('Content-Type: ' . $file['mime_type']);
        header('Content-Length: ' . $file['size']);
        header('Content-Disposition: inline; filename="' . $file['original_name'] . '"');
        header('Cache-Control: public, max-age=3600');

        readfile($file['file_path']);
        exit;
    }

    /**
     * Descargar carpeta compartida como ZIP
     */
    public function downloadZip() {
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

        // Solo permitir ZIP para carpetas
        if ($shareDetails['entity_type'] !== 'folder') {
            die('Esta función solo está disponible para carpetas compartidas');
        }

        $folder_id = $shareDetails['entity_id'];
        $files = $this->fileModel->getByFolder($folder_id);

        if (empty($files)) {
            die('La carpeta no contiene archivos');
        }

        // Obtener nombre de la carpeta
        $folder = $this->folderModel->getById($folder_id);
        $folderName = $folder ? $folder['name'] : 'carpeta';

        // Verificar que la extensión ZIP esté disponible
        if (!class_exists('ZipArchive')) {
            die('Error: La extensión ZIP de PHP no está habilitada. Para habilitarla en XAMPP, edita el archivo php.ini y descomenta la línea: extension=zip');
        }

        // Crear archivo ZIP temporal
        $zipFileName = $folderName . '_' . date('Y-m-d_H-i-s') . '.zip';
        $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            die('No se pudo crear el archivo ZIP');
        }

        // Agregar archivos al ZIP
        foreach ($files as $file) {
            if (file_exists($file['file_path'])) {
                $zip->addFile($file['file_path'], $file['original_name']);
            }
        }

        $zip->close();

        // Verificar que el ZIP se creó correctamente
        if (!file_exists($zipFilePath)) {
            die('Error al crear el archivo ZIP');
        }

        // Enviar el archivo ZIP
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        header('Content-Length: ' . filesize($zipFilePath));
        header('Pragma: public');
        header('Cache-Control: must-revalidate');

        readfile($zipFilePath);

        // Eliminar archivo temporal
        unlink($zipFilePath);

        exit;
    }

    /**
     * Eliminar enlace compartido
     */
    public function delete() {
        AuthController::requirePermission('can_share');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $share_id = isset($_POST['share_id']) ? (int)$_POST['share_id'] : 0;
        $folder_id = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' ? (int)$_POST['folder_id'] : null;

        // Convertir 0 a null
        if ($folder_id === 0) {
            $folder_id = null;
        }

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
        AuthController::requirePermission('can_share');

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
