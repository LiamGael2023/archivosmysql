<?php
// Extraer variables del array $data
if (isset($data) && is_array($data)) {
    extract($data);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Archivos</title>

    <!-- Tabler CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=3.1">
</head>
<body>
    <div class="page">
        <!-- Navbar -->
        <header class="navbar navbar-expand-md navbar-light d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="<?= BASE_URL ?>">
                        <i class="ti ti-folders icon me-2"></i>
                        Sistema de Archivos
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="d-none d-md-flex me-3">
                        <form method="GET" class="input-icon">
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Buscar archivos..." />
                            <span class="input-icon-addon">
                                <i class="ti ti-search"></i>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-wrapper">
            <!-- Sidebar -->
            <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <h1 class="navbar-brand d-lg-none">
                        <a href="<?= BASE_URL ?>">
                            <i class="ti ti-folders icon me-2"></i>
                            Archivos
                        </a>
                    </h1>
                    <div class="collapse navbar-collapse" id="sidebar-menu">
                        <ul class="navbar-nav pt-lg-3">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-home"></i>
                                    </span>
                                    <span class="nav-link-title">Inicio</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle show" href="#navbar-extra" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="true">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-folders"></i>
                                    </span>
                                    <span class="nav-link-title">Carpetas</span>
                                </a>
                                <div class="dropdown-menu show">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            <?php renderTreeTabler($folderTree, $currentFolder); ?>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>

            <!-- Page Content -->
            <div class="page-body">
                <div class="container-xl">
                    <!-- Alerts -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-check icon alert-icon"></i>
                                </div>
                                <div>
                                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                                </div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-alert-circle icon alert-icon"></i>
                                </div>
                                <div>
                                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                </div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    <?php endif; ?>

                    <!-- Breadcrumb -->
                    <div class="page-header d-print-none">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="ti ti-home"></i> Inicio</a></li>
                                        <?php foreach ($breadcrumb as $bc): ?>
                                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?folder=<?= $bc['id'] ?>"><?= htmlspecialchars($bc['name']) ?></a></li>
                                        <?php endforeach; ?>
                                    </ol>
                                </nav>
                                <h2 class="page-title">
                                    <?php if (!empty($breadcrumb)): ?>
                                        <?= htmlspecialchars(end($breadcrumb)['name']) ?>
                                    <?php else: ?>
                                        Todos los archivos
                                    <?php endif; ?>
                                </h2>
                            </div>
                            <div class="col-auto ms-auto d-print-none">
                                <div class="btn-list">
                                    <button class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#newFolderModal">
                                        <i class="ti ti-folder-plus icon"></i>
                                        Nueva Carpeta
                                    </button>
                                    <button class="btn btn-success d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                                        <i class="ti ti-upload icon"></i>
                                        Subir Archivo
                                    </button>
                                    <!-- Mobile buttons -->
                                    <button class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#newFolderModal">
                                        <i class="ti ti-folder-plus icon"></i>
                                    </button>
                                    <button class="btn btn-success d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                                        <i class="ti ti-upload icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters and Sorting -->
                    <div class="row row-cards mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label class="form-label">Ordenar por</label>
                                            <select class="form-select" onchange="updateOrder(this.value)">
                                                <option value="name" <?= $orderBy === 'name' ? 'selected' : '' ?>>Nombre</option>
                                                <option value="id" <?= $orderBy === 'id' ? 'selected' : '' ?>>ID</option>
                                                <option value="created_at" <?= $orderBy === 'created_at' ? 'selected' : '' ?>>Fecha de Creación</option>
                                                <option value="size" <?= $orderBy === 'size' ? 'selected' : '' ?>>Tamaño</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Dirección</label>
                                            <select class="form-select" onchange="updateDirection(this.value)">
                                                <option value="ASC" <?= $orderDir === 'ASC' ? 'selected' : '' ?>>Ascendente</option>
                                                <option value="DESC" <?= $orderDir === 'DESC' ? 'selected' : '' ?>>Descendente</option>
                                            </select>
                                        </div>
                                        <?php if (!empty($allMetaKeys)): ?>
                                        <div class="col-md-7">
                                            <form method="GET" class="row g-2">
                                                <?php if ($currentFolder): ?>
                                                    <input type="hidden" name="folder" value="<?= $currentFolder ?>">
                                                <?php endif; ?>
                                                <div class="col-md-5">
                                                    <label class="form-label">Filtrar por metakey</label>
                                                    <select name="filter_key" class="form-select" onchange="this.form.submit()">
                                                        <option value="">-- Seleccionar --</option>
                                                        <?php foreach ($allMetaKeys as $key): ?>
                                                            <option value="<?= htmlspecialchars($key) ?>" <?= $filterKey === $key ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($key) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <?php if ($filterKey): ?>
                                                <div class="col-md-4">
                                                    <label class="form-label">Valor</label>
                                                    <input type="text" name="filter_value" class="form-control" placeholder="Valor (opcional)" value="<?= htmlspecialchars($filterValue) ?>">
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                                                    <a href="<?= BASE_URL . ($currentFolder ? '?folder=' . $currentFolder : '') ?>" class="btn btn-secondary">Limpiar</a>
                                                </div>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Folders Grid -->
                    <?php if (!empty($folders)): ?>
                    <div class="row row-cards mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="ti ti-folders icon me-2"></i>Carpetas</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row row-cards">
                                        <?php foreach ($folders as $folder): ?>
                                        <div class="col-sm-6 col-lg-4 col-xl-3">
                                            <div class="card card-sm folder-card">
                                                <a href="<?= BASE_URL ?>?folder=<?= $folder['id'] ?>" class="d-block">
                                                    <div class="card-body text-center">
                                                        <div class="text-muted mb-3">
                                                            <i class="ti ti-folder icon" style="font-size: 3rem; color: #FDB927;"></i>
                                                        </div>
                                                        <div class="fw-bold"><?= htmlspecialchars($folder['name']) ?></div>
                                                    </div>
                                                </a>
                                                <div class="card-footer">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button class="btn btn-sm btn-ghost-secondary" onclick="event.preventDefault(); renameFolder(<?= $folder['id'] ?>, '<?= htmlspecialchars($folder['name'], ENT_QUOTES) ?>')">
                                                            <i class="ti ti-edit icon"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-ghost-info" onclick="event.preventDefault(); shareFolder(<?= $folder['id'] ?>)">
                                                            <i class="ti ti-share icon"></i>
                                                        </button>
                                                        <?php if ($folder['id'] != 1): ?>
                                                        <button class="btn btn-sm btn-ghost-danger" onclick="event.preventDefault(); deleteFolder(<?= $folder['id'] ?>)">
                                                            <i class="ti ti-trash icon"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Files Table -->
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
                                            No hay archivos en esta ubicación
                                        </p>
                                        <div class="empty-action">
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                                                <i class="ti ti-upload icon"></i>
                                                Subir primer archivo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th class="d-none d-md-table-cell">Extensión</th>
                                                <th class="d-none d-lg-table-cell">Tamaño</th>
                                                <th class="d-none d-xl-table-cell">Fecha</th>
                                                <th class="d-none d-xl-table-cell">Metakeys</th>
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
                                                            <div class="fw-bold"><?= htmlspecialchars($file['name']) ?></div>
                                                            <div class="text-muted small"><?= htmlspecialchars($file['original_name']) ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <span class="badge bg-azure"><?= strtoupper(htmlspecialchars($file['extension'])) ?></span>
                                                </td>
                                                <td class="text-muted d-none d-lg-table-cell"><?= formatSize($file['size']) ?></td>
                                                <td class="text-muted d-none d-xl-table-cell">
                                                    <div><?= date('Y-m-d', strtotime($file['created_at'])) ?></div>
                                                    <div class="small"><?= date('H:i', strtotime($file['created_at'])) ?></div>
                                                </td>
                                                <td class="d-none d-xl-table-cell">
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <?php foreach ($file['meta_keys'] as $meta): ?>
                                                            <span class="badge bg-blue-lt">
                                                                <?= htmlspecialchars($meta['meta_key']) ?>
                                                                <?php if ($meta['meta_value']): ?>
                                                                    : <?= htmlspecialchars($meta['meta_value']) ?>
                                                                <?php endif; ?>
                                                                <a href="#" onclick="event.preventDefault(); deleteMeta(<?= $meta['id'] ?>)" class="ms-1 text-reset">×</a>
                                                            </span>
                                                        <?php endforeach; ?>
                                                        <button class="badge bg-success" onclick="addMeta(<?= $file['id'] ?>)" style="border:none;">
                                                            <i class="ti ti-plus icon"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-list flex-nowrap">
                                                        <a href="<?= BASE_URL ?>/file/download?id=<?= $file['id'] ?>" class="btn btn-sm btn-ghost-primary" title="Descargar">
                                                            <i class="ti ti-download icon"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-ghost-secondary" onclick="renameFile(<?= $file['id'] ?>, '<?= htmlspecialchars($file['name'], ENT_QUOTES) ?>')" title="Renombrar">
                                                            <i class="ti ti-edit icon"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-ghost-info" onclick="shareFile(<?= $file['id'] ?>)" title="Compartir">
                                                            <i class="ti ti-share icon"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-ghost-danger" onclick="deleteFile(<?= $file['id'] ?>)" title="Eliminar">
                                                            <i class="ti ti-trash icon"></i>
                                                        </button>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Nueva Carpeta -->
    <div class="modal modal-blur fade" id="newFolderModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti ti-folder-plus icon me-2"></i>Nueva Carpeta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/folder/create">
                    <div class="modal-body">
                        <input type="hidden" name="parent_id" value="<?= $currentFolder ?>">
                        <div class="mb-3">
                            <label class="form-label required">Nombre de la carpeta</label>
                            <input type="text" name="name" class="form-control" placeholder="Mi carpeta" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Carpeta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Subir Archivo -->
    <div class="modal modal-blur fade" id="uploadFileModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content" style="max-height: 90vh;">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti ti-upload icon me-2"></i>Subir Archivos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/file/upload" enctype="multipart/form-data" id="uploadFilesForm">
                    <div class="modal-body">
                        <input type="hidden" name="folder_id" value="<?= $currentFolder ?>">
                        <div class="mb-3">
                            <label class="form-label required">Seleccionar archivo(s)</label>
                            <input type="file" name="files[]" id="fileInput" class="form-control" multiple required onchange="handleFileSelect(this)">
                            <small class="form-hint">Puedes seleccionar múltiples archivos. Tamaño máximo por archivo: 100MB</small>
                        </div>

                        <!-- Contenedor dinámico para archivos seleccionados -->
                        <div id="filesMetaContainer"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-upload icon"></i> Subir Archivos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabler Core -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/js/tabler.min.js"></script>

    <!-- Custom JS -->
    <script src="<?= BASE_URL ?>/js/main.js"></script>
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        const currentFolder = <?= $currentFolder ?: 'null' ?>;
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

function renderTreeTabler($tree, $currentFolder, $level = 0) {
    foreach ($tree as $folder) {
        $isActive = $currentFolder == $folder['id'] ? 'active' : '';
        $padding = $level * 20;
        echo '<a class="dropdown-item ' . $isActive . '" href="' . BASE_URL . '?folder=' . $folder['id'] . '" style="padding-left: ' . (16 + $padding) . 'px;">';
        echo '<i class="ti ti-folder icon me-2"></i>';
        echo htmlspecialchars($folder['name']);
        echo '</a>';
        if (!empty($folder['children'])) {
            renderTreeTabler($folder['children'], $currentFolder, $level + 1);
        }
    }
}
?>
