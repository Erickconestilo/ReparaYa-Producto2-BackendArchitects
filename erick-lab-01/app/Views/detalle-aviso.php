<!doctype html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Detalle de reparación | ReparaYa</title><link rel="stylesheet" href="/assets/reparaya.css"></head>
<body class="interior">
    <?php require __DIR__ . '/_cabecera.php'; ?>
    <h1>Incidencia <?= htmlspecialchars($aviso['localizador'], ENT_QUOTES, 'UTF-8') ?></h1>
    <p><a href="/mis-avisos">Volver a mis avisos</a></p>
    <?php if (($_GET['cancelada'] ?? '') === 'si'): ?><p>Solicitud cancelada.</p><?php endif; ?>
    <?php if (($_GET['actualizado'] ?? '') === 'si'): ?><p>Datos de la solicitud actualizados.</p><?php endif; ?>
    <p>Servicio: <?= htmlspecialchars($aviso['nombre_especialidad'], ENT_QUOTES, 'UTF-8') ?></p>
    <p>Fecha: <?= htmlspecialchars($aviso['fecha_servicio'], ENT_QUOTES, 'UTF-8') ?> · Franja: <?= htmlspecialchars($aviso['franja_horaria'], ENT_QUOTES, 'UTF-8') ?></p>
    <p>Tipo: <?= htmlspecialchars($aviso['tipo_urgencia'], ENT_QUOTES, 'UTF-8') ?> · Estado: <?= htmlspecialchars($aviso['estado'], ENT_QUOTES, 'UTF-8') ?></p>
    <p>Avería: <?= nl2br(htmlspecialchars($aviso['descripcion'], ENT_QUOTES, 'UTF-8')) ?></p>
    <p>Dirección: <?= htmlspecialchars($aviso['direccion'], ENT_QUOTES, 'UTF-8') ?> · Contacto: <?= htmlspecialchars($aviso['telefono_contacto'], ENT_QUOTES, 'UTF-8') ?></p>
    <h2>Historial de estados</h2>
    <ul>
        <?php foreach ($historial as $cambio): ?>
            <li><?= htmlspecialchars($cambio['fecha_cambio'], ENT_QUOTES, 'UTF-8') ?>:
                <?= htmlspecialchars((string) ($cambio['estado_anterior'] ?? 'Sin estado'), ENT_QUOTES, 'UTF-8') ?> →
                <?= htmlspecialchars($cambio['estado_nuevo'], ENT_QUOTES, 'UTF-8') ?>
                (<?= htmlspecialchars($cambio['actor'], ENT_QUOTES, 'UTF-8') ?>)
                <?php if ($cambio['observacion'] !== null): ?>
                    — <?= htmlspecialchars($cambio['observacion'], ENT_QUOTES, 'UTF-8') ?>
                <?php endif; ?></li>
        <?php endforeach; ?>
    </ul>
    <?php if ($puedeEditar): ?>
        <p><a href="/aviso/editar?id=<?= (int) $aviso['id'] ?>">Corregir avería, dirección o teléfono</a></p>
    <?php endif; ?>
    <?php if (in_array($aviso['estado'], ['Pendiente', 'Asignada'], true)): ?>
        <form action="/aviso/cancelar" method="post">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= (int) $aviso['id'] ?>">
            <button type="submit">Solicitar cancelación (solo si faltan más de 48 h)</button>
        </form>
    <?php endif; ?>
</body>
</html>
