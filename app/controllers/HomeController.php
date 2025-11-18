<?php
/**
 * Controlador Home - Página principal con vista de archivos y carpetas
 */
class HomeController {
    private $fileModel;
    private $folderModel;
    private $metaKeyModel;

    public function __construct() {
        $this->fileModel = new File();
        $this->folderModel = new Folder();
        $this->metaKeyModel = new MetaKey();
    }

    /**
     * Vista principal
     */
    public function index() {
        // Requerir autenticación
        AuthController::requireAuth();

        $folder_id = isset($_GET['folder']) ? (int)$_GET['folder'] : null;
        $orderBy = isset($_GET['order']) ? $_GET['order'] : 'name';
        $orderDir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filterKey = isset($_GET['filter_key']) ? trim($_GET['filter_key']) : '';
        $filterValue = isset($_GET['filter_value']) ? trim($_GET['filter_value']) : '';

        // Obtener archivos
        if ($search) {
            $files = $this->fileModel->search($search, $orderBy, $orderDir);
        } elseif ($filterKey) {
            $files = $this->fileModel->filterByMetaKey($filterKey, $filterValue ?: null, $orderBy, $orderDir);
        } else {
            $files = $this->fileModel->getByFolder($folder_id, $orderBy, $orderDir);
        }

        // Obtener carpetas actuales
        $folders = $this->folderModel->getByParent($folder_id);

        // Obtener árbol de carpetas
        $folderTree = $this->folderModel->getTree();

        // Obtener ruta actual (breadcrumb)
        $breadcrumb = $folder_id ? $this->folderModel->getPath($folder_id) : [];

        // Obtener metakeys para archivos
        $filesWithMeta = [];
        foreach ($files as $file) {
            $file['meta_keys'] = $this->metaKeyModel->getByEntity('file', $file['id']);
            $filesWithMeta[] = $file;
        }

        // Obtener todos los metakeys únicos para el filtro
        $allMetaKeys = $this->metaKeyModel->getAllUniqueKeys();

        // Obtener usuario actual y permisos
        $currentUser = AuthController::getCurrentUser();

        $data = [
            'files' => $filesWithMeta,
            'folders' => $folders,
            'folderTree' => $folderTree,
            'breadcrumb' => $breadcrumb,
            'currentFolder' => $folder_id,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir,
            'search' => $search,
            'filterKey' => $filterKey,
            'filterValue' => $filterValue,
            'allMetaKeys' => $allMetaKeys,
            'currentUser' => $currentUser
        ];

        require_once __DIR__ . '/../views/home.php';
    }
}
