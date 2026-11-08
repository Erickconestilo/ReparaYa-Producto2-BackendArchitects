<?php
/** Vista inspirada en el calendario mes/semana/dia del ZIP de Paula Gomez del Aguila. */
$esc = static fn (mixed $valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$porDia = [];
foreach ($incidencias as $incidencia) {
    $porDia[substr($incidencia['fecha_servicio'], 0, 10)][] = $incidencia;
}
$meses = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
          'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
$mesBase = $fechaSeleccionada->modify('first day of this month');
$anterior = $vista === 'mes' ? $mesBase->modify('-1 month')
    : $fechaSeleccionada->modify($vista === 'semana' ? '-7 days' : '-1 day');
$siguiente = $vista === 'mes' ? $mesBase->modify('+1 month')
    : $fechaSeleccionada->modify($vista === 'semana' ? '+7 days' : '+1 day');
$url = static fn (string $modo, DateTimeImmutable $fecha): string =>
    '/admin/calendario?vista=' . $modo . '&fecha=' . $fecha->format('Y-m-d');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendario de reparaciones | ReparaYa</title>
    <style>
        body { font: 16px/1.5 system-ui,sans-serif; max-width: 1150px; margin: 1.5rem auto; padding: 0 1rem; color: #172633; }
        a { color: #165a89; }
        nav { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; margin: 1rem 0; }
        .tabla { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #cbd5df; padding: .5rem; vertical-align: top; }
        th { background: #ecf1f5; }
        td { min-width: 95px; height: 100px; }
        .fuera { background: #f4f6f8; color: #6e7880; }
        .evento { display: block; padding: .35rem; margin: .3rem 0; background: #e1eefb; border-left: 3px solid #2b679e; font-size: .82rem; }
        .urgente { background: #ffeded; border-left-color: #b52c33; }
        .lista { list-style: none; padding: 0; }
        .lista li { padding: .5rem; margin: .4rem 0; border-left: 3px solid #2b679e; background: #eff5fa; }
    </style>
</head>
<body>
    <h1>Calendario de reparaciones</h1>
    <p><a href="/admin">Volver a administracion</a></p>
    <nav aria-label="Vistas del calendario">
        <?php foreach (['mes' => 'Mes', 'semana' => 'Semana', 'dia' => 'Dia'] as $modo => $etiqueta): ?>
            <a href="<?= $esc($url($modo, $fechaSeleccionada)) ?>"
               <?= $vista === $modo ? 'aria-current="page"' : '' ?>><?= $etiqueta ?></a>
        <?php endforeach; ?>
    </nav>
    <nav aria-label="Cambiar periodo">
        <a href="<?= $esc($url($vista, $anterior)) ?>">Periodo anterior</a>
        <strong><?= $vista === 'mes'
            ? $esc(ucfirst($meses[(int) $fechaSeleccionada->format('n')]) . ' ' . $fechaSeleccionada->format('Y'))
            : $esc($inicio->format('d/m/Y') . ' – ' . $fin->modify('-1 day')->format('d/m/Y')) ?></strong>
        <a href="<?= $esc($url($vista, $siguiente)) ?>">Periodo siguiente</a>
    </nav>

    <?php if ($vista === 'mes'): ?>
        <div class="tabla"><table>
            <thead><tr><?php foreach ($dias as $dia): ?><th scope="col"><?= $esc($dia) ?></th><?php endforeach; ?></tr></thead>
            <tbody>
            <?php for ($semana = 0; $semana < 6; $semana++): ?>
                <tr>
                <?php for ($dia = 0; $dia < 7; $dia++): ?>
                    <?php $fechaDia = $inicio->modify('+' . ($semana * 7 + $dia) . ' days');
                          $clave = $fechaDia->format('Y-m-d'); ?>
                    <td class="<?= $fechaDia->format('m') !== $fechaSeleccionada->format('m') ? 'fuera' : '' ?>">
                        <strong><?= $esc($fechaDia->format('j')) ?></strong>
                        <?php foreach ($porDia[$clave] ?? [] as $incidencia): ?>
                            <span class="evento <?= $incidencia['tipo_urgencia'] === 'Urgente' ? 'urgente' : '' ?>">
                                <?= $esc($incidencia['localizador']) ?>
                                <small><?= $esc($incidencia['estado']) ?></small>
                            </span>
                        <?php endforeach; ?>
                    </td>
                <?php endfor; ?>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table></div>
    <?php else: ?>
        <?php $numeroDias = $vista === 'semana' ? 7 : 1; ?>
        <?php for ($indice = 0; $indice < $numeroDias; $indice++): ?>
            <?php $fechaDia = $inicio->modify('+' . $indice . ' days');
                  $clave = $fechaDia->format('Y-m-d'); ?>
            <h2><?= $esc($dias[(int) $fechaDia->format('N') - 1]) ?> <?= $esc($fechaDia->format('d/m/Y')) ?></h2>
            <?php if (empty($porDia[$clave])): ?><p>Sin reparaciones programadas.</p><?php endif; ?>
            <ul class="lista">
            <?php foreach ($porDia[$clave] ?? [] as $incidencia): ?>
                <li>
                    <strong><?= $esc($incidencia['localizador']) ?></strong> —
                    <?= $esc($incidencia['nombre_especialidad']) ?> — <?= $esc($incidencia['estado']) ?><br>
                    <?= $esc($incidencia['fecha_servicio']) ?>;
                    cliente: <?= $esc($incidencia['cliente']) ?>;
                    tecnico: <?= $esc($incidencia['tecnico'] ?? 'sin asignar') ?>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endfor; ?>
    <?php endif; ?>
</body>
</html>
