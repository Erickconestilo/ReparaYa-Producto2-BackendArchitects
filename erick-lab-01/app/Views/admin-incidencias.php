<?php
$esc = static fn (mixed $valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Administración de reparaciones | ReparaYa</title><link rel="stylesheet" href="/assets/reparaya.css"></head>
<body class="interior">
    <?php require __DIR__ . '/_cabecera.php'; ?>
    <h1>Administración de reparaciones</h1>
    <p><a href="/perfil">Mi perfil</a> · <a href="/admin/calendario">Calendario</a> · <a href="/admin/servicios">Servicios</a> · <a href="/admin/tecnicos">Técnicos</a></p>
    <?php if (($_GET['asignada'] ?? '') === 'si'): ?>
        <p role="status">La reparación se ha asignado correctamente.</p>
    <?php endif; ?>
    <p>Solo se puede asignar una reparación pendiente a un técnico disponible de su especialidad.</p>
    <form method="get" action="/admin">
        <label for="estado">Filtrar por estado</label>
        <select id="estado" name="estado">
            <option value="">Todos los estados</option>
            <?php foreach ($estadosValidos as $estado): ?>
                <option value="<?= $esc($estado) ?>" <?= $estadoFiltro === $estado ? 'selected' : '' ?>><?= $esc($estado) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrar</button>
        <?php if ($estadoFiltro !== ''): ?><a href="/admin">Mostrar todas</a><?php endif; ?>
    </form>
    <div class="tabla-desplazable"><table>
        <thead><tr><th>Localizador</th><th>Cliente</th><th>Servicio</th><th>Cita</th><th>Estado</th><th>Técnico</th><th>Acción</th></tr></thead>
        <tbody>
        <?php foreach ($incidencias as $incidencia): ?>
            <tr>
                <td><?= $esc($incidencia['localizador']) ?></td>
                <td><?= $esc($incidencia['cliente']) ?></td>
                <td><?= $esc($incidencia['nombre_especialidad']) ?></td>
                <td><?= $esc($incidencia['fecha_servicio']) ?></td>
                <td><?= $esc($incidencia['estado']) ?></td>
                <td><?= $esc($incidencia['tecnico'] ?? 'Sin asignar') ?></td>
                <td>
                <?php if ($incidencia['estado'] === 'Pendiente'): ?>
                    <form method="post" action="/admin/asignar">
                        <input type="hidden" name="_csrf" value="<?= $esc($csrfToken) ?>">
                        <input type="hidden" name="incidencia_id" value="<?= (int) $incidencia['id'] ?>">
                        <label>Asignar a
                            <select name="tecnico_id" required>
                                <option value="">Seleccionar</option>
                                <?php foreach ($tecnicos as $tecnico): ?>
                                    <?php if ((int) $tecnico['especialidad_id'] === (int) ($incidencia['especialidad_id'] ?? 0)): ?>
                                        <option value="<?= (int) $tecnico['id'] ?>"><?= $esc($tecnico['nombre_completo']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="submit">Asignar</button>
                    </form>
                <?php else: ?>—<?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php if (!$incidencias): ?><p><?= $estadoFiltro === '' ? 'No hay reparaciones registradas.' : 'No hay reparaciones con ese estado.' ?></p><?php endif; ?>
</body>
</html>
