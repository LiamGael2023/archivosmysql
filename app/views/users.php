<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Archivos</title>
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/css/tabler.min.css">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
</head>
<body class="theme-dark">
    <div class="page">
        <!-- Navbar -->
        <header class="navbar navbar-expand-md navbar-dark d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="<?= BASE_URL ?>" class="d-flex align-items-center text-decoration-none">
                        <img src="https://www.chavimochic.gob.pe/sgrhi_app/assets/images/logo/logoPECH.png" alt="Logo" style="height: 40px; margin-right: 10px;">
                        <span class="text-white">Sistema de Archivos</span>
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm bg-blue-lt">
                                <i class="ti ti-user"></i>
                            </span>
                            <div class="d-none d-xl-block ps-2">
                                <div><?= htmlspecialchars($currentUser['full_name'] ?: $currentUser['username']) ?></div>
                                <div class="mt-1 small text-muted"><?= htmlspecialchars($currentUser['role_name']) ?></div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="<?= BASE_URL ?>" class="dropdown-item">
                                <i class="ti ti-home icon me-2"></i>Inicio
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?= BASE_URL ?>/logout" class="dropdown-item">
                                <i class="ti ti-logout icon me-2"></i>Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page content -->
        <div class="page-wrapper">
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="page-pretitle">Administración</div>
                    <h2 class="page-title">
                        <i class="ti ti-users icon me-2"></i>
                        Gestión de Usuarios
                    </h2>
                </div>
            </div>

            <div class="page-body">
                <div class="container-xl">
                    <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div><i class="ti ti-check icon me-2"></i></div>
                            <div><?= htmlspecialchars($_SESSION['success']) ?></div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    <?php unset($_SESSION['success']); endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div><i class="ti ti-alert-circle icon me-2"></i></div>
                            <div><?= htmlspecialchars($_SESSION['error']) ?></div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    <?php unset($_SESSION['error']); endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Usuarios del Sistema</h3>
                            <div class="card-actions">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                                    <i class="ti ti-plus icon me-1"></i>
                                    Nuevo Usuario
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Nombre Completo</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Último Acceso</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <span class="avatar avatar-sm bg-blue-lt me-2">
                                                <?= strtoupper(substr($user['username'], 0, 2)) ?>
                                            </span>
                                            <?= htmlspecialchars($user['username']) ?>
                                        </td>
                                        <td class="text-muted"><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['full_name'] ?: '-') ?></td>
                                        <td>
                                            <?php
                                            $roleBadge = 'bg-secondary';
                                            switch ($user['role_name']) {
                                                case 'admin': $roleBadge = 'bg-red'; break;
                                                case 'editor': $roleBadge = 'bg-blue'; break;
                                                case 'collaborator': $roleBadge = 'bg-green'; break;
                                                case 'viewer': $roleBadge = 'bg-yellow'; break;
                                            }
                                            ?>
                                            <span class="badge <?= $roleBadge ?>"><?= htmlspecialchars($user['role_name']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($user['is_active']): ?>
                                            <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted">
                                            <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Nunca' ?>
                                        </td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <button class="btn btn-sm btn-primary" onclick="editUser(<?= $user['id'] ?>)">
                                                    <i class="ti ti-edit icon"></i>
                                                </button>
                                                <?php if ($user['id'] != 1 && $user['id'] != $currentUser['id']): ?>
                                                <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>')">
                                                    <i class="ti ti-trash icon"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Roles Info -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Permisos por Rol</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Rol</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Subir</th>
                                        <th class="text-center">Editar</th>
                                        <th class="text-center">Eliminar</th>
                                        <th class="text-center">Compartir</th>
                                        <th class="text-center">Usuarios</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $role): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($role['name']) ?></strong></td>
                                        <td class="text-muted"><?= htmlspecialchars($role['description']) ?></td>
                                        <td class="text-center">
                                            <?= $role['can_upload'] ? '<i class="ti ti-check text-success"></i>' : '<i class="ti ti-x text-danger"></i>' ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $role['can_edit'] ? '<i class="ti ti-check text-success"></i>' : '<i class="ti ti-x text-danger"></i>' ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $role['can_delete'] ? '<i class="ti ti-check text-success"></i>' : '<i class="ti ti-x text-danger"></i>' ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $role['can_share'] ? '<i class="ti ti-check text-success"></i>' : '<i class="ti ti-x text-danger"></i>' ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $role['can_manage_users'] ? '<i class="ti ti-check text-success"></i>' : '<i class="ti ti-x text-danger"></i>' ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Crear Usuario -->
    <div class="modal modal-blur fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="<?= BASE_URL ?>/user/create">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Usuario</label>
                                <input type="text" name="username" class="form-control" required
                                       pattern="[a-zA-Z0-9_]+" title="Solo letras, números y guión bajo">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Contraseña</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="full_name" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Rol</label>
                            <select name="role_id" class="form-select" required>
                                <option value="">Seleccione un rol</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?> - <?= htmlspecialchars($role['description']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check icon me-1"></i>
                            Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Editar Usuario -->
    <div class="modal modal-blur fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="<?= BASE_URL ?>/user/update">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Usuario</label>
                                <input type="text" name="username" id="editUsername" class="form-control" required
                                       pattern="[a-zA-Z0-9_]+" title="Solo letras, números y guión bajo">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Email</label>
                                <input type="email" name="email" id="editEmail" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control" minlength="6"
                                       placeholder="Dejar vacío para mantener la actual">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="full_name" id="editFullName" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Rol</label>
                                <select name="role_id" id="editRoleId" class="form-select" required>
                                    <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <label class="form-check form-switch mt-2">
                                    <input type="checkbox" name="is_active" id="editIsActive" class="form-check-input">
                                    <span class="form-check-label">Usuario Activo</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check icon me-1"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Eliminar Usuario -->
    <div class="modal modal-blur fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <i class="ti ti-alert-triangle icon mb-2 text-danger icon-lg"></i>
                    <h3>¿Eliminar usuario?</h3>
                    <div class="text-muted" id="deleteUserMessage">El usuario será eliminado permanentemente.</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                            <div class="col">
                                <form method="POST" action="<?= BASE_URL ?>/user/delete" id="deleteUserForm">
                                    <input type="hidden" name="user_id" id="deleteUserId">
                                    <button type="submit" class="btn btn-danger w-100">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabler JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/js/tabler.min.js"></script>
    <script>
        const BASE_URL = '<?= BASE_URL ?>';

        function editUser(userId) {
            fetch(BASE_URL + '/user/get?id=' + userId)
                .then(response => response.json())
                .then(user => {
                    if (user.error) {
                        alert(user.error);
                        return;
                    }

                    document.getElementById('editUserId').value = user.id;
                    document.getElementById('editUsername').value = user.username;
                    document.getElementById('editEmail').value = user.email;
                    document.getElementById('editFullName').value = user.full_name || '';
                    document.getElementById('editRoleId').value = user.role_id;
                    document.getElementById('editIsActive').checked = user.is_active == 1;

                    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos del usuario');
                });
        }

        function deleteUser(userId, username) {
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteUserMessage').textContent =
                'El usuario "' + username + '" será eliminado permanentemente.';

            const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            modal.show();
        }
    </script>
</body>
</html>
