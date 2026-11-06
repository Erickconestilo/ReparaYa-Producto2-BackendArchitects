<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/Database/Conexion.php';

$urgente = $argv[1] ?? '';
$estandar = $argv[2] ?? '';
if (!preg_match('/^REP-[0-9]{4}-[A-F0-9]{8}$/', $urgente)
    || !preg_match('/^REP-[0-9]{4}-[A-F0-9]{8}$/', $estandar)) {
    exit(2);
}

$consulta = Conexion::abrir()->prepare(
    'SELECT i.tipo_urgencia, i.estado, COUNT(h.id) AS cambios
     FROM incidencias i JOIN historial_estados h ON h.incidencia_id = i.id
     WHERE i.localizador = :codigo GROUP BY i.id, i.tipo_urgencia, i.estado'
);
function comprobar(PDOStatement $consulta, string $codigo, string $tipo, string $estado, int $cambios): bool
{
    $consulta->execute(['codigo' => $codigo]);
    $fila = $consulta->fetch();
    return $fila !== false && $fila['tipo_urgencia'] === $tipo
        && $fila['estado'] === $estado && (int) $fila['cambios'] === $cambios;
}
$correcto = comprobar($consulta, $urgente, 'Urgente', 'Pendiente', 1)
    && comprobar($consulta, $estandar, 'Estándar', 'Cancelada', 2);
echo 'REGLAS_ESTADOS_HISTORIAL=' . ($correcto ? 'PASS' : 'FAIL') . PHP_EOL;
exit($correcto ? 0 : 1);
