<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enlaces Compartidos</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Enlaces Compartidos</h1>
        </header>

        <main class="content">
            <?php if (empty($links)): ?>
                <p>No hay enlaces compartidos para este elemento</p>
            <?php else: ?>
            <table class="files-table">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>URL</th>
                        <th>Estado</th>
                        <th>Expira</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($links as $link): ?>
                    <tr>
                        <td><code><?= substr($link['token'], 0, 16) ?>...</code></td>
                        <td>
                            <a href="<?= BASE_URL ?>/share?token=<?= $link['token'] ?>" target="_blank">
                                Ver enlace
                            </a>
                        </td>
                        <td>
                            <?php if ($link['is_active']): ?>
                                <span class="status active">Activo</span>
                            <?php else: ?>
                                <span class="status inactive">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($link['expires_at']): ?>
                                <?= date('Y-m-d H:i', strtotime($link['expires_at'])) ?>
                            <?php else: ?>
                                Nunca
                            <?php endif; ?>
                        </td>
                        <td><?= date('Y-m-d H:i', strtotime($link['created_at'])) ?></td>
                        <td>
                            <form method="POST" action="<?= BASE_URL ?>/share/delete" style="display:inline;">
                                <input type="hidden" name="share_id" value="<?= $link['id'] ?>">
                                <button type="submit" onclick="return confirm('¿Eliminar este enlace?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <br>
            <a href="<?= BASE_URL ?>">Volver al inicio</a>
        </main>
    </div>
</body>
</html>
