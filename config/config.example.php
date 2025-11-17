<?php
/**
 * Archivo de configuración de ejemplo
 * Copiar este archivo como config.php y actualizar los valores
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');
define('DB_NAME', 'filemanager_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('BASE_URL', 'http://localhost/archivosmysql/public');
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('MAX_UPLOAD_SIZE', 100 * 1024 * 1024); // 100MB

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Habilitar reporte de errores en desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);
