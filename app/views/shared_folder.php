<?php
// Extraer variables del array $data si existe
if (isset($data) && is_array($data)) {
    extract($data);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carpeta Compartida - <?= htmlspecialchars($shareDetails['entity_name']) ?></title>

    <!-- Tabler CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="page">
        <div class="page-wrapper">
            <div class="container-xl py-4">
                <!-- Header -->
                <div class="page-header d-print-none mb-4">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <div class="page-pretitle">
                                <i class="ti ti-share icon me-1"></i> Carpeta Compartida
                            </div>
                            <h2 class="page-title">
                                <i class="ti ti-folder icon me-2" style="color: #FDB927;"></i>
                                <?= htmlspecialchars($shareDetails['entity_name']) ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- Folders -->
                <?php if (!empty($folders)): ?>
                <div class="row row-cards mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="ti ti-folders icon me-2"></i>Carpetas</h3>
                            </div>
                            <div class="list-group list-group-flush">
                                <?php foreach ($folders as $folder): ?>
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <i class="ti ti-folder icon" style="font-size: 2rem; color: #FDB927;"></i>
                                        </div>
                                        <div class="col text-truncate">
                                            <div class="fw-bold"><?= htmlspecialchars($folder['name']) ?></div>
                                            <div class="text-muted small">Carpeta</div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Files -->
                <div class="row row-cards">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="ti ti-files icon me-2"></i>Archivos (<?= count($files) ?>)</h3>
                            </div>
                            <?php if (empty($files)): ?>
                            <div class="card-body">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-file-x icon"></i>
                                    </div>
                                    <p class="empty-title">No hay archivos</p>
                                    <p class="empty-subtitle text-muted">
                                        Esta carpeta no contiene archivos
                                    </p>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th class="d-none d-md-table-cell">Tamaño</th>
                                            <th class="d-none d-lg-table-cell">Fecha de Creación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($files as $file): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-sm me-2" style="background-color: <?= getFileColor($file['extension']) ?>">
                                                        <i class="ti ti-file-text"></i>
                                                    </span>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($file['original_name']) ?></div>
                                                        <div class="text-muted small">
                                                            <span class="badge bg-azure"><?= strtoupper(htmlspecialchars($file['extension'])) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-muted d-none d-md-table-cell">
                                                <?= formatSize($file['size']) ?>
                                            </td>
                                            <td class="text-muted d-none d-lg-table-cell">
                                                <div><?= date('Y-m-d', strtotime($file['created_at'])) ?></div>
                                                <div class="small"><?= date('H:i', strtotime($file['created_at'])) ?></div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted text-center">
                                    <i class="ti ti-info-circle icon me-1"></i>
                                    Esta es una carpeta compartida públicamente. Los archivos están disponibles solo para visualización.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabler Core -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/js/tabler.min.js"></script>
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

function getFileColor($extension) {
    $colors = [
        'pdf' => '#dc3545',
        'doc' => '#0d6efd',
        'docx' => '#0d6efd',
        'xls' => '#198754',
        'xlsx' => '#198754',
        'ppt' => '#fd7e14',
        'pptx' => '#fd7e14',
        'zip' => '#6610f2',
        'rar' => '#6610f2',
        'jpg' => '#d63384',
        'jpeg' => '#d63384',
        'png' => '#d63384',
        'gif' => '#d63384',
        'txt' => '#6c757d',
        'default' => '#206bc4'
    ];
    return $colors[strtolower($extension)] ?? $colors['default'];
}
?>
