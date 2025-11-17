<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Archivos</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistema de Gestión de Archivos</h1>
        </header>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="main-content">
            <!-- Sidebar con árbol de carpetas -->
            <aside class="sidebar">
                <h3>Árbol de Carpetas</h3>
                <div class="folder-tree">
                    <?php
                    function renderTree($tree, $currentFolder) {
                        echo '<ul>';
                        foreach ($tree as $folder) {
                            $isActive = $currentFolder == $folder['id'] ? 'active' : '';
                            echo '<li class="' . $isActive . '">';
                            echo '<a href="' . BASE_URL . '?folder=' . $folder['id'] . '">';
                            echo str_repeat('&nbsp;&nbsp;', $folder['level']) . '📁 ' . htmlspecialchars($folder['name']);
                            echo '</a>';
                            if (!empty($folder['children'])) {
                                renderTree($folder['children'], $currentFolder);
                            }
                            echo '</li>';
                        }
                        echo '</ul>';
                    }
                    renderTree($folderTree, $currentFolder);
                    ?>
                </div>
            </aside>

            <!-- Contenido principal -->
            <main class="content">
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <a href="<?= BASE_URL ?>">🏠 Inicio</a>
                    <?php foreach ($breadcrumb as $bc): ?>
                        / <a href="<?= BASE_URL ?>?folder=<?= $bc['id'] ?>"><?= htmlspecialchars($bc['name']) ?></a>
                    <?php endforeach; ?>
                </div>

                <!-- Barra de herramientas -->
                <div class="toolbar">
                    <div class="toolbar-left">
                        <button onclick="toggleModal('newFolderModal')">📁 Nueva Carpeta</button>
                        <button onclick="toggleModal('uploadFileModal')">📤 Subir Archivo</button>
                    </div>

                    <div class="toolbar-right">
                        <form method="GET" class="search-form">
                            <input type="text" name="search" placeholder="Buscar..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit">🔍</button>
                        </form>
                    </div>
                </div>

                <!-- Filtros y ordenamiento -->
                <div class="filters">
                    <div class="filter-group">
                        <label>Ordenar por:</label>
                        <select onchange="updateOrder(this.value)">
                            <option value="name" <?= $orderBy === 'name' ? 'selected' : '' ?>>Nombre</option>
                            <option value="id" <?= $orderBy === 'id' ? 'selected' : '' ?>>ID</option>
                            <option value="created_at" <?= $orderBy === 'created_at' ? 'selected' : '' ?>>Fecha de Creación</option>
                            <option value="size" <?= $orderBy === 'size' ? 'selected' : '' ?>>Tamaño</option>
                        </select>

                        <select onchange="updateDirection(this.value)">
                            <option value="ASC" <?= $orderDir === 'ASC' ? 'selected' : '' ?>>Ascendente</option>
                            <option value="DESC" <?= $orderDir === 'DESC' ? 'selected' : '' ?>>Descendente</option>
                        </select>
                    </div>

                    <?php if (!empty($allMetaKeys)): ?>
                    <div class="filter-group">
                        <label>Filtrar por metakey:</label>
                        <form method="GET" class="inline-form">
                            <?php if ($currentFolder): ?>
                                <input type="hidden" name="folder" value="<?= $currentFolder ?>">
                            <?php endif; ?>
                            <select name="filter_key" onchange="this.form.submit()">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($allMetaKeys as $key): ?>
                                    <option value="<?= htmlspecialchars($key) ?>" <?= $filterKey === $key ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($key) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($filterKey): ?>
                                <input type="text" name="filter_value" placeholder="Valor (opcional)" value="<?= htmlspecialchars($filterValue) ?>">
                                <button type="submit">Filtrar</button>
                                <a href="<?= BASE_URL . ($currentFolder ? '?folder=' . $currentFolder : '') ?>">Limpiar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Lista de carpetas -->
                <?php if (!empty($folders)): ?>
                <section class="folders-section">
                    <h3>Carpetas</h3>
                    <div class="items-grid">
                        <?php foreach ($folders as $folder): ?>
                        <div class="item folder-item">
                            <div class="item-icon">📁</div>
                            <div class="item-info">
                                <div class="item-name">
                                    <a href="<?= BASE_URL ?>?folder=<?= $folder['id'] ?>">
                                        <?= htmlspecialchars($folder['name']) ?>
                                    </a>
                                </div>
                            </div>
                            <div class="item-actions">
                                <button onclick="renameFolder(<?= $folder['id'] ?>, '<?= htmlspecialchars($folder['name']) ?>')">✏️</button>
                                <button onclick="shareFolder(<?= $folder['id'] ?>)">🔗</button>
                                <?php if ($folder['id'] != 1): ?>
                                <button onclick="deleteFolder(<?= $folder['id'] ?>)">🗑️</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Lista de archivos -->
                <section class="files-section">
                    <h3>Archivos (<?= count($files) ?>)</h3>
                    <?php if (empty($files)): ?>
                        <p class="empty-message">No hay archivos en esta ubicación</p>
                    <?php else: ?>
                    <table class="files-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Extensión</th>
                                <th>Tamaño</th>
                                <th>Fecha de Creación</th>
                                <th>Metakeys</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($files as $file): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($file['name']) ?></strong><br>
                                    <small><?= htmlspecialchars($file['original_name']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($file['extension']) ?></td>
                                <td><?= formatSize($file['size']) ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($file['created_at'])) ?></td>
                                <td>
                                    <div class="meta-tags">
                                        <?php foreach ($file['meta_keys'] as $meta): ?>
                                            <span class="meta-tag">
                                                <?= htmlspecialchars($meta['meta_key']) ?>
                                                <?php if ($meta['meta_value']): ?>
                                                    : <?= htmlspecialchars($meta['meta_value']) ?>
                                                <?php endif; ?>
                                                <button onclick="deleteMeta(<?= $meta['id'] ?>)" class="delete-meta">×</button>
                                            </span>
                                        <?php endforeach; ?>
                                        <button onclick="addMeta(<?= $file['id'] ?>)" class="add-meta-btn">+ Agregar</button>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>/file/download?id=<?= $file['id'] ?>" class="btn-small">⬇️</a>
                                        <button onclick="renameFile(<?= $file['id'] ?>, '<?= htmlspecialchars($file['name']) ?>')" class="btn-small">✏️</button>
                                        <button onclick="shareFile(<?= $file['id'] ?>)" class="btn-small">🔗</button>
                                        <button onclick="deleteFile(<?= $file['id'] ?>)" class="btn-small">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>

    <!-- Modal: Nueva Carpeta -->
    <div id="newFolderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleModal('newFolderModal')">&times;</span>
            <h2>Nueva Carpeta</h2>
            <form method="POST" action="<?= BASE_URL ?>/folder/create">
                <input type="hidden" name="parent_id" value="<?= $currentFolder ?>">
                <label>Nombre de la carpeta:</label>
                <input type="text" name="name" required>
                <button type="submit">Crear</button>
            </form>
        </div>
    </div>

    <!-- Modal: Subir Archivo -->
    <div id="uploadFileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleModal('uploadFileModal')">&times;</span>
            <h2>Subir Archivo</h2>
            <form method="POST" action="<?= BASE_URL ?>/file/upload" enctype="multipart/form-data">
                <input type="hidden" name="folder_id" value="<?= $currentFolder ?>">
                <label>Seleccionar archivo:</label>
                <input type="file" name="file" required>

                <div id="metaKeysContainer">
                    <label>Metakeys (opcional):</label>
                    <div class="meta-input">
                        <input type="text" name="meta_keys[0][key]" placeholder="Clave">
                        <input type="text" name="meta_keys[0][value]" placeholder="Valor">
                    </div>
                </div>
                <button type="button" onclick="addMetaInput()">+ Agregar Metakey</button>

                <button type="submit">Subir</button>
            </form>
        </div>
    </div>

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
?>
