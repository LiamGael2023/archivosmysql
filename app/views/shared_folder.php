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
    <title><?= $shareDetails['entity_type'] === 'file' ? 'Archivo' : 'Carpeta' ?> Compartido - <?= htmlspecialchars($shareDetails['entity_name']) ?></title>

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
                                <i class="ti ti-share icon me-1"></i>
                                <?= $shareDetails['entity_type'] === 'file' ? 'Archivo Compartido' : 'Carpeta Compartida' ?>
                            </div>
                            <h2 class="page-title">
                                <?php if ($shareDetails['entity_type'] === 'file'): ?>
                                    <i class="ti ti-file icon me-2" style="color: #206bc4;"></i>
                                <?php else: ?>
                                    <i class="ti ti-folder icon me-2" style="color: #FDB927;"></i>
                                <?php endif; ?>
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
                                            <th class="w-1">Acciones</th>
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
                                            <td>
                                                <div class="btn-list flex-nowrap">
                                                    <button class="btn btn-sm btn-primary" onclick="previewSharedFile(<?= $file['id'] ?>, '<?= htmlspecialchars($file['original_name'], ENT_QUOTES) ?>', '<?= strtolower($file['extension']) ?>', '<?= $token ?>')" title="Visualizar">
                                                        <i class="ti ti-eye icon"></i>
                                                    </button>
                                                    <a href="<?= BASE_URL ?>/share/download?token=<?= $token ?>&file_id=<?= $file['id'] ?>" class="btn btn-sm btn-success" title="Descargar">
                                                        <i class="ti ti-download icon"></i>
                                                    </a>
                                                </div>
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
                                    <?php if ($shareDetails['entity_type'] === 'file'): ?>
                                        Este archivo ha sido compartido públicamente. Puedes visualizarlo o descargarlo.
                                    <?php else: ?>
                                        Esta carpeta ha sido compartida públicamente. Puedes visualizar o descargar los archivos.
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Previsualizar Archivo -->
    <div class="modal modal-blur fade" id="previewFileModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewFileTitle">
                        <i class="ti ti-eye icon me-2"></i>
                        <span id="previewFileName">Vista previa</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="previewFileContent">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <a href="#" id="previewDownloadBtn" class="btn btn-primary">
                        <i class="ti ti-download icon"></i> Descargar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabler Core -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/js/tabler.min.js"></script>

    <script>
        const BASE_URL = '<?= BASE_URL ?>';

        // Función auxiliar para escapar HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Previsualizar archivo compartido
        function previewSharedFile(fileId, fileName, extension, token) {
            const modal = new bootstrap.Modal(document.getElementById('previewFileModal'));
            const contentContainer = document.getElementById('previewFileContent');
            const fileNameSpan = document.getElementById('previewFileName');
            const downloadBtn = document.getElementById('previewDownloadBtn');

            // Actualizar título y botón de descarga
            fileNameSpan.textContent = fileName;
            downloadBtn.href = BASE_URL + '/share/download?token=' + token + '&file_id=' + fileId;

            // Extensiones de imagen
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
            // Extensiones de PDF
            const pdfExtensions = ['pdf'];
            // Extensiones de texto/código
            const textExtensions = ['txt', 'html', 'css', 'js', 'json', 'xml', 'md', 'php', 'py', 'java', 'c', 'cpp', 'h'];
            // Extensiones de video
            const videoExtensions = ['mp4', 'webm', 'ogg'];
            // Extensiones de audio
            const audioExtensions = ['mp3', 'wav', 'ogg', 'flac'];

            const previewUrl = BASE_URL + '/share/preview?token=' + token + '&file_id=' + fileId;

            // Generar contenido según el tipo de archivo
            if (imageExtensions.includes(extension)) {
                contentContainer.innerHTML = `
                    <div class="text-center p-4">
                        <img src="${previewUrl}" alt="${fileName}" class="img-fluid" style="max-height: 70vh;">
                    </div>
                `;
            } else if (pdfExtensions.includes(extension)) {
                contentContainer.innerHTML = `
                    <div style="height: 70vh;">
                        <iframe src="${previewUrl}" width="100%" height="100%" style="border: none;"></iframe>
                    </div>
                `;
            } else if (videoExtensions.includes(extension)) {
                contentContainer.innerHTML = `
                    <div class="text-center p-4">
                        <video controls class="w-100" style="max-height: 70vh;">
                            <source src="${previewUrl}" type="video/${extension}">
                            Tu navegador no soporta la reproducción de video.
                        </video>
                    </div>
                `;
            } else if (audioExtensions.includes(extension)) {
                contentContainer.innerHTML = `
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="ti ti-music icon" style="font-size: 4rem; color: #206bc4;"></i>
                        </div>
                        <h4>${fileName}</h4>
                        <audio controls class="w-100 mt-3">
                            <source src="${previewUrl}" type="audio/${extension === 'mp3' ? 'mpeg' : extension}">
                            Tu navegador no soporta la reproducción de audio.
                        </audio>
                    </div>
                `;
            } else if (textExtensions.includes(extension)) {
                fetch(previewUrl)
                    .then(response => response.text())
                    .then(text => {
                        contentContainer.innerHTML = `
                            <div class="p-3" style="max-height: 70vh; overflow-y: auto;">
                                <pre class="mb-0" style="white-space: pre-wrap; word-wrap: break-word;"><code>${escapeHtml(text)}</code></pre>
                            </div>
                        `;
                    })
                    .catch(error => {
                        contentContainer.innerHTML = `
                            <div class="text-center p-4">
                                <div class="empty">
                                    <div class="empty-icon text-danger">
                                        <i class="ti ti-alert-circle icon" style="font-size: 3rem;"></i>
                                    </div>
                                    <p class="empty-title">Error al cargar el archivo</p>
                                </div>
                            </div>
                        `;
                    });
            } else {
                // Archivo no previsualizable
                const fileIcons = {
                    'doc': 'ti-file-type-doc',
                    'docx': 'ti-file-type-docx',
                    'xls': 'ti-file-type-xls',
                    'xlsx': 'ti-file-type-xls',
                    'ppt': 'ti-file-type-ppt',
                    'pptx': 'ti-file-type-ppt',
                    'zip': 'ti-file-zip',
                    'rar': 'ti-file-zip',
                    '7z': 'ti-file-zip',
                    'exe': 'ti-app-window',
                    'default': 'ti-file'
                };

                const iconClass = fileIcons[extension] || fileIcons['default'];

                contentContainer.innerHTML = `
                    <div class="text-center p-4">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="${iconClass} icon" style="font-size: 4rem; color: #6c757d;"></i>
                            </div>
                            <p class="empty-title">${fileName}</p>
                            <p class="empty-subtitle text-muted">
                                Este tipo de archivo (.${extension.toUpperCase()}) no se puede previsualizar directamente.
                            </p>
                            <div class="empty-action">
                                <a href="${BASE_URL}/share/download?token=${token}&file_id=${fileId}" class="btn btn-primary">
                                    <i class="ti ti-download icon"></i> Descargar archivo
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }

            modal.show();
        }
    </script>
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
