<?php $esc = static fn (mixed $valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); ?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Reparacion asignada | ReparaYa</title><link rel="stylesheet" href="/assets/reparaya.css"></head>
<body class="interior">
    <?php require __DIR__ . '/_cabecera.php'; ?>
    <p><a href="/tecnico">Volver a mis reparaciones</a></p>
    <h1>Reparacion <?= $esc($incidencia['localizador']) ?></h1>
    <?php if (($_GET['estado'] ?? '') === 'ok'): ?><p role="status">Estado actualizado correctamente.</p><?php endif; ?>
    <dl>
        <dt>Servicio</dt><dd><?= $esc($incidencia['nombre_especialidad']) ?></dd>
        <dt>Estado</dt><dd><?= $esc($incidencia['estado']) ?></dd>
        <dt>Fecha</dt><dd><?= $esc($incidencia['fecha_servicio']) ?></dd>
        <dt>Direccion</dt><dd><?= $esc($incidencia['direccion']) ?></dd>
        <dt>Averia</dt><dd><?= nl2br($esc($incidencia['descripcion'])) ?></dd>
    </dl>
    <?php
        $estadoSiguiente = $incidencia['estado'] === 'Asignada' ? 'En curso'
            : ($incidencia['estado'] === 'En curso' ? 'Finalizada' : null);
    ?>
    <?php if ($estadoSiguiente !== null): ?>
        <form method="post" action="/tecnico/aviso/estado">
            <input type="hidden" name="_csrf" value="<?= $esc($csrfToken) ?>">
            <input type="hidden" name="incidencia_id" value="<?= (int) $incidencia['id'] ?>">
            <input type="hidden" name="nuevo_estado" value="<?= $esc($estadoSiguiente) ?>">
            <button type="submit">Marcar como <?= $esc($estadoSiguiente) ?></button>
        </form>
    <?php else: ?><p>Esta reparacion no admite mas cambios de estado desde el panel tecnico.</p><?php endif; ?>

    <h2>Historial de estados</h2>
    <ol>
        <?php foreach ($historial as $evento): ?>
            <li><?= $esc($evento['fecha_cambio']) ?>:
                <?= $esc($evento['estado_anterior'] ?? 'Alta') ?> → <?= $esc($evento['estado_nuevo']) ?>
                (<?= $esc($evento['actor']) ?>)
            </li>
        <?php endforeach; ?>
    </ol>
</body>
</html>
