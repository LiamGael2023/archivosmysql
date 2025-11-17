<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enlaces Compartidos</title>

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
                            <h2 class="page-title">
                                <i class="ti ti-link icon me-2"></i>
                                Enlaces Compartidos
                            </h2>
                        </div>
                        <div class="col-auto ms-auto d-print-none">
                            <a href="<?= BASE_URL ?>" class="btn btn-primary">
                                <i class="ti ti-arrow-left icon"></i>
                                Volver al inicio
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Links Table -->
                <div class="row row-cards">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="ti ti-share icon me-2"></i>Listado de Enlaces</h3>
                            </div>
                            <?php if (empty($links)): ?>
                            <div class="card-body">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-link-off icon"></i>
                                    </div>
                                    <p class="empty-title">No hay enlaces compartidos</p>
                                    <p class="empty-subtitle text-muted">
                                        No se han creado enlaces compartidos para este elemento
                                    </p>
                                    <div class="empty-action">
                                        <a href="<?= BASE_URL ?>" class="btn btn-primary">
                                            <i class="ti ti-arrow-left icon"></i>
                                            Volver al inicio
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Token</th>
                                            <th class="d-none d-lg-table-cell">URL</th>
                                            <th>Estado</th>
                                            <th class="d-none d-md-table-cell">Expira</th>
                                            <th class="d-none d-xl-table-cell">Creado</th>
                                            <th class="w-1">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($links as $link): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-key icon me-2 text-muted"></i>
                                                    <code class="small"><?= substr($link['token'], 0, 16) ?>...</code>
                                                </div>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="<?= BASE_URL ?>/share?token=<?= $link['token'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="ti ti-external-link icon"></i>
                                                        Ver enlace
                                                    </a>
                                                    <button class="btn btn-sm btn-ghost-secondary" onclick="copyToClipboard('<?= BASE_URL ?>/share?token=<?= $link['token'] ?>')" title="Copiar URL">
                                                        <i class="ti ti-copy icon"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($link['is_active']): ?>
                                                    <span class="badge bg-success">
                                                        <i class="ti ti-check icon"></i> Activo
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="ti ti-x icon"></i> Inactivo
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted d-none d-md-table-cell">
                                                <?php if ($link['expires_at']): ?>
                                                    <div>
                                                        <i class="ti ti-clock icon me-1"></i>
                                                        <?= date('Y-m-d', strtotime($link['expires_at'])) ?>
                                                    </div>
                                                    <div class="small"><?= date('H:i', strtotime($link['expires_at'])) ?></div>
                                                <?php else: ?>
                                                    <span class="badge bg-cyan-lt">
                                                        <i class="ti ti-infinity icon"></i> Sin expiración
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted d-none d-xl-table-cell">
                                                <div><?= date('Y-m-d', strtotime($link['created_at'])) ?></div>
                                                <div class="small"><?= date('H:i', strtotime($link['created_at'])) ?></div>
                                            </td>
                                            <td>
                                                <form method="POST" action="<?= BASE_URL ?>/share/delete" style="display:inline;">
                                                    <input type="hidden" name="share_id" value="<?= $link['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-ghost-danger" onclick="return confirm('¿Eliminar este enlace?')" title="Eliminar">
                                                        <i class="ti ti-trash icon"></i>
                                                    </button>
                                                </form>
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
            </div>
        </div>
    </div>

    <!-- Tabler Core -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/js/tabler.min.js"></script>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('URL copiada al portapapeles');
            }, function(err) {
                console.error('Error al copiar: ', err);
            });
        }
    </script>
</body>
</html>
