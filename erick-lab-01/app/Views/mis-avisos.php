<!doctype html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Mis avisos | ReparaYa</title><link rel="stylesheet" href="/assets/reparaya.css"></head>
<body class="interior">
    <?php require __DIR__ . '/_cabecera.php'; ?>
    <h1>Mis avisos</h1>
    <p><a href="/solicitar">Crear solicitud</a> · <a href="/perfil">Mi perfil</a></p>
    <?php if (isset($_GET['creada'])): ?><p>Solicitud guardada: <?= htmlspecialchars((string) $_GET['creada'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <?php if ($avisos === []): ?>
        <p>Aún no tienes solicitudes.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($avisos as $aviso): ?>
                <li><a href="/aviso?id=<?= (int) $aviso['id'] ?>"><?= htmlspecialchars($aviso['localizador'], ENT_QUOTES, 'UTF-8') ?></a>
                — <?= htmlspecialchars($aviso['nombre_especialidad'], ENT_QUOTES, 'UTF-8') ?>
                — <?= htmlspecialchars($aviso['fecha_servicio'], ENT_QUOTES, 'UTF-8') ?>
                — <?= htmlspecialchars($aviso['tipo_urgencia'], ENT_QUOTES, 'UTF-8') ?>
                — Estado: <?= htmlspecialchars($aviso['estado'], ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
