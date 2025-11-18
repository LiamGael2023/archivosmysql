<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Archivos</title>
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/css/tabler.min.css">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
        }
        .login-logo {
            font-size: 3rem;
            color: #206bc4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="card card-md">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="https://www.chavimochic.gob.pe/sgrhi_app/assets/images/logo/logoPECH.png" alt="Logo" style="max-width: 150px; margin-bottom: 1rem;">
                        <h2 class="h2 mt-2">Sistema de Archivos</h2>
                        <p class="text-muted">Ingrese sus credenciales para continuar</p>
                    </div>

                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div><i class="ti ti-alert-circle icon me-2"></i></div>
                            <div><?= htmlspecialchars($error) ?></div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/auth/login" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Usuario o Email</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="ti ti-user"></i>
                                </span>
                                <input type="text" name="username" class="form-control"
                                       placeholder="Ingrese su usuario o email" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="ti ti-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control"
                                       placeholder="Ingrese su contraseña" required>
                            </div>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-login icon me-2"></i>
                                Iniciar Sesión
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center text-white mt-3">
                <small>&copy; <?= date('Y') ?> Sistema de Archivos</small>
            </div>
        </div>
    </div>

    <!-- Tabler JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/js/tabler.min.js"></script>
</body>
</html>
