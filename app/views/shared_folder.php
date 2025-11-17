<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carpeta Compartida</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📁 Carpeta Compartida: <?= htmlspecialchars($shareDetails['entity_name']) ?></h1>
        </header>

        <main class="content">
            <?php if (!empty($folders)): ?>
            <section class="folders-section">
                <h3>Carpetas</h3>
                <ul>
                    <?php foreach ($folders as $folder): ?>
                    <li>📁 <?= htmlspecialchars($folder['name']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>

            <section class="files-section">
                <h3>Archivos</h3>
                <?php if (empty($files)): ?>
                    <p>No hay archivos en esta carpeta</p>
                <?php else: ?>
                <table class="files-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tamaño</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $file): ?>
                        <tr>
                            <td><?= htmlspecialchars($file['original_name']) ?></td>
                            <td><?= formatSize($file['size']) ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($file['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>

<?php
function formatSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}
?>
