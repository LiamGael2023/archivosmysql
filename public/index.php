<?php
/**
 * Punto de entrada principal de la aplicación
 */

// Iniciar sesión
session_start();

// Incluir configuración
require_once __DIR__ . '/../config/config.php';

// Autoload de modelos y controladores
spl_autoload_register(function ($class) {
    $modelPath = __DIR__ . '/../app/models/' . $class . '.php';
    $controllerPath = __DIR__ . '/../app/controllers/' . $class . '.php';

    if (file_exists($modelPath)) {
        require_once $modelPath;
    } elseif (file_exists($controllerPath)) {
        require_once $controllerPath;
    }
});

// Obtener la ruta solicitada
$request = $_SERVER['REQUEST_URI'];
$base_path = '/archivosmysql/public';
$request = str_replace($base_path, '', parse_url($request, PHP_URL_PATH));

// Enrutamiento simple
switch ($request) {
    case '/':
    case '':
        $controller = new HomeController();
        $controller->index();
        break;

    // Rutas de archivos
    case '/file/upload':
        $controller = new FileController();
        $controller->upload();
        break;

    case '/file/rename':
        $controller = new FileController();
        $controller->rename();
        break;

    case '/file/delete':
        $controller = new FileController();
        $controller->delete();
        break;

    case '/file/download':
        $controller = new FileController();
        $controller->download();
        break;

    case '/file/preview':
        $controller = new FileController();
        $controller->preview();
        break;

    case '/file/add-meta':
        $controller = new FileController();
        $controller->addMetaKey();
        break;

    case '/file/delete-meta':
        $controller = new FileController();
        $controller->deleteMetaKey();
        break;

    // Rutas de carpetas
    case '/folder/create':
        $controller = new FolderController();
        $controller->create();
        break;

    case '/folder/rename':
        $controller = new FolderController();
        $controller->rename();
        break;

    case '/folder/delete':
        $controller = new FolderController();
        $controller->delete();
        break;

    // Rutas de compartir
    case '/share':
        $controller = new ShareController();
        $controller->view();
        break;

    case '/share/download':
        $controller = new ShareController();
        $controller->download();
        break;

    case '/share/preview':
        $controller = new ShareController();
        $controller->preview();
        break;

    case '/share/download-zip':
        $controller = new ShareController();
        $controller->downloadZip();
        break;

    case '/share/create':
        $controller = new ShareController();
        $controller->create();
        break;

    case '/share/delete':
        $controller = new ShareController();
        $controller->delete();
        break;

    case '/share/list':
        $controller = new ShareController();
        $controller->listLinks();
        break;

    default:
        http_response_code(404);
        echo '404 - Página no encontrada';
        break;
}
