<?php
declare(strict_types=1);

// Comprueba SQL real tras la prueba HTTP. Solo procesa códigos de registros de ensayo.
require_once dirname(__DIR__) . '/app/Database/Conexion.php';

if ($argc !== 3) {
    fwrite(STDERR, "Uso: php tests/comprobar-edicion-ficticia.php CODIGO_EDITADO CODIGO_URGENTE\n");
    exit(2);
}

$pdo = Conexion::abrir();
$consulta = $pdo->prepare(
    'SELECT id, estado, descripcion, direccion, telefono_contacto FROM incidencias WHERE localizador = :codigo'
);
$consulta->execute(['codigo' => $argv[1]]);
$editado = $consulta->fetch();
$consulta->execute(['codigo' => $argv[2]]);
$urgente = $consulta->fetch();

if (!$editado || !$urgente
    || $editado['estado'] !== 'Cancelada'
    || $editado['direccion'] !== 'Avenida Demo 25'
    || $editado['telefono_contacto'] !== '699999999'
    || !str_contains($editado['descripcion'], '<script>')
    || $urgente['estado'] !== 'Pendiente') {
    fwrite(STDERR, "FAIL: no coincide la edición almacenada en MySQL.\n");
    exit(1);
}

$historial = $pdo->prepare(
    'SELECT COUNT(*) FROM historial_estados
     WHERE incidencia_id = :id AND observacion = :nota'
);
$historial->execute([
    'id' => $editado['id'],
    'nota' => 'Datos de contacto y avería actualizados por el cliente',
]);
if ((int) $historial->fetchColumn() !== 1) {
    fwrite(STDERR, "FAIL: edición sin historial o historial duplicado.\n");
    exit(1);
}

echo "EDICION_Y_HISTORIAL_BD=PASS\n";
