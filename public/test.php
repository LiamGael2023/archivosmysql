<?php
/**
 * Archivo de diagnóstico - Verificar configuración
 */
session_start();
require_once __DIR__ . '/../config/config.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico del Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Diagnóstico del Sistema de Archivos</h1>

        <h2>1. Configuración</h2>
        <table>
            <tr>
                <th>Parámetro</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>BASE_URL</td>
                <td><code><?= BASE_URL ?></code></td>
            </tr>
            <tr>
                <td>DB_HOST</td>
                <td><code><?= DB_HOST ?></code></td>
            </tr>
            <tr>
                <td>DB_PORT</td>
                <td><code><?= DB_PORT ?></code></td>
            </tr>
            <tr>
                <td>DB_NAME</td>
                <td><code><?= DB_NAME ?></code></td>
            </tr>
            <tr>
                <td>UPLOAD_DIR</td>
                <td><code><?= UPLOAD_DIR ?></code></td>
            </tr>
            <tr>
                <td>MAX_UPLOAD_SIZE</td>
                <td><code><?= formatBytes(MAX_UPLOAD_SIZE) ?></code></td>
            </tr>
        </table>

        <h2>2. Conexión a Base de Datos</h2>
        <?php
        try {
            require_once __DIR__ . '/../app/models/Database.php';
            $db = Database::getInstance();
            $conn = $db->getConnection();

            // Verificar si la base de datos existe
            $stmt = $conn->query("SELECT DATABASE() as db");
            $result = $stmt->fetch();

            echo '<div class="status success">';
            echo '✅ Conexión exitosa a la base de datos: <strong>' . $result['db'] . '</strong>';
            echo '</div>';

            // Verificar tablas
            $stmt = $conn->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            echo '<p><strong>Tablas encontradas:</strong></p>';
            echo '<ul>';
            $expectedTables = ['folders', 'files', 'meta_keys', 'shared_links'];
            foreach ($expectedTables as $table) {
                if (in_array($table, $tables)) {
                    echo '<li>✅ ' . $table . '</li>';
                } else {
                    echo '<li>❌ ' . $table . ' (faltante)</li>';
                }
            }
            echo '</ul>';

        } catch (Exception $e) {
            echo '<div class="status error">';
            echo '❌ Error de conexión: ' . $e->getMessage();
            echo '</div>';
        }
        ?>

        <h2>3. Permisos de Directorios</h2>
        <?php
        $uploadsDir = __DIR__ . '/../uploads';
        if (is_dir($uploadsDir)) {
            if (is_writable($uploadsDir)) {
                echo '<div class="status success">✅ Directorio uploads/ existe y es escribible</div>';
            } else {
                echo '<div class="status error">❌ Directorio uploads/ existe pero NO es escribible</div>';
                echo '<p>Ejecuta: <code>chmod -R 777 uploads/</code></p>';
            }
        } else {
            echo '<div class="status error">❌ Directorio uploads/ no existe</div>';
        }
        ?>

        <h2>4. Módulos PHP</h2>
        <table>
            <tr>
                <th>Extensión</th>
                <th>Estado</th>
            </tr>
            <?php
            $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'fileinfo', 'json'];
            foreach ($extensions as $ext) {
                $loaded = extension_loaded($ext);
                echo '<tr>';
                echo '<td>' . $ext . '</td>';
                echo '<td>' . ($loaded ? '✅ Cargada' : '❌ No cargada') . '</td>';
                echo '</tr>';
            }
            ?>
        </table>

        <h2>5. Configuración PHP</h2>
        <table>
            <tr>
                <th>Directiva</th>
                <th>Valor Actual</th>
            </tr>
            <tr>
                <td>upload_max_filesize</td>
                <td><?= ini_get('upload_max_filesize') ?></td>
            </tr>
            <tr>
                <td>post_max_size</td>
                <td><?= ini_get('post_max_size') ?></td>
            </tr>
            <tr>
                <td>max_execution_time</td>
                <td><?= ini_get('max_execution_time') ?>s</td>
            </tr>
            <tr>
                <td>memory_limit</td>
                <td><?= ini_get('memory_limit') ?></td>
            </tr>
        </table>

        <h2>6. Rutas del Sistema</h2>
        <div class="status info">
            <p><strong>Archivo actual:</strong> <?= __FILE__ ?></p>
            <p><strong>Directorio raíz:</strong> <?= dirname(__DIR__) ?></p>
            <p><strong>URL de prueba:</strong> <?= $_SERVER['REQUEST_URI'] ?></p>
            <p><strong>Método HTTP:</strong> <?= $_SERVER['REQUEST_METHOD'] ?></p>
        </div>

        <h2>7. Acciones</h2>
        <a href="<?= BASE_URL ?>" class="btn">🏠 Ir a la Aplicación</a>
        <a href="<?= BASE_URL ?>/test.php" class="btn">🔄 Recargar Diagnóstico</a>

        <br><br>
        <div class="status info">
            <strong>Nota:</strong> Las rutas como <code>/folder/create</code> o <code>/file/upload</code> solo funcionan con método POST (desde formularios), no desde el navegador directamente.
        </div>
    </div>
</body>
</html>

<?php
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>
