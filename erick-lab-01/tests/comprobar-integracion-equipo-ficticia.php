<?php
declare(strict_types=1);

// Proteccion: esta prueba crea datos y SOLO puede ejecutarse en una BD efimera.
if (getenv('INTEGRATION_TEST_EPHEMERAL') !== '1'
    || getenv('DB_NAME') !== 'uoc_p2_integracion_prueba'
    || getenv('DB_HOST') !== '127.0.0.1') {
    fwrite(STDERR, "BLOQUEADO: prueba exclusiva de una base aislada de integracion.\n");
    exit(3);
}

require_once dirname(__DIR__) . '/app/Models/GestionEquipo.php';
require_once dirname(__DIR__) . '/app/Models/Solicitud.php';

$pdo = Conexion::abrir();
$pases = 0;
function prueba(bool $correcto, string $descripcion): void
{
    global $pases;
    if (!$correcto) { throw new RuntimeException('FAIL: ' . $descripcion); }
    $pases++;
    echo 'PASS: ' . $descripcion . PHP_EOL;
}
function insertarUsuario(PDO $pdo, string $rol, string $identificador): int
{
    $consulta = $pdo->prepare(
        'INSERT INTO usuarios (nombre, email, password, rol)
         VALUES (:nombre, :email, :hash, :rol)'
    );
    $consulta->execute([
        'nombre' => 'Persona ficticia ' . $rol,
        'email' => $identificador . '-' . $rol . '-' . bin2hex(random_bytes(2)) . '@example.test',
        'hash' => password_hash('Solo-Ensayo-12345', PASSWORD_DEFAULT), 'rol' => $rol,
    ]);
    return (int) $pdo->lastInsertId();
}
function insertarTecnico(PDO $pdo, int $usuarioId, int $especialidadId): int
{
    $consulta = $pdo->prepare(
        'INSERT INTO tecnicos (usuario_id, nombre_completo, especialidad_id, disponible)
         VALUES (:usuario, :nombre, :especialidad, 1)'
    );
    $consulta->execute([
        'usuario' => $usuarioId, 'nombre' => 'Tecnico inventado', 'especialidad' => $especialidadId,
    ]);
    return (int) $pdo->lastInsertId();
}

$especialidades = $pdo->query('SELECT id FROM especialidades ORDER BY id LIMIT 2')->fetchAll(PDO::FETCH_COLUMN);
if (count($especialidades) !== 2) { throw new RuntimeException('Se requieren dos especialidades DEMO.'); }
$id = bin2hex(random_bytes(3));
$admin = insertarUsuario($pdo, 'admin', $id);
$cliente = insertarUsuario($pdo, 'particular', $id);
$tecnicoUsuario = insertarUsuario($pdo, 'tecnico', $id);
$tecnicoAjenoUsuario = insertarUsuario($pdo, 'tecnico', $id);
$tecnico = insertarTecnico($pdo, $tecnicoUsuario, (int) $especialidades[0]);
$tecnicoAjeno = insertarTecnico($pdo, $tecnicoAjenoUsuario, (int) $especialidades[1]);

$codigo = 'REP-2026-' . strtoupper(bin2hex(random_bytes(4)));
$consulta = $pdo->prepare(
    "INSERT INTO incidencias (localizador, cliente_id, especialidad_id, descripcion, direccion,
                             fecha_servicio, franja_horaria, telefono_contacto, tipo_urgencia, estado)
     VALUES (:codigo, :cliente, :especialidad, :descripcion, :direccion, :fecha,
             'manana', '600000000', 'Estándar', 'Pendiente')"
);
$consulta->execute([
    'codigo' => $codigo, 'cliente' => $cliente, 'especialidad' => (int) $especialidades[0],
    'descripcion' => 'Averia totalmente ficticia para las pruebas',
    'direccion' => 'Calle de los Ensayos 1 (DEMO)',
    'fecha' => (new DateTimeImmutable('+7 days'))->format('Y-m-d 09:00:00'),
]);
$incidenciaId = (int) $pdo->lastInsertId();
$historial = $pdo->prepare(
    "INSERT INTO historial_estados (incidencia_id, estado_anterior, estado_nuevo, actor_id, observacion)
     VALUES (:id, NULL, 'Pendiente', :cliente, 'Alta de prueba')"
);
$historial->execute(['id' => $incidenciaId, 'cliente' => $cliente]);

$fechaPrueba = (new DateTimeImmutable('+7 days'))->format('Y-m-d');
$agendaDelDia = GestionEquipo::calendarioDesdeHasta($fechaPrueba . ' 00:00:00',
    (new DateTimeImmutable($fechaPrueba))->modify('+1 day')->format('Y-m-d 00:00:00'));
prueba(count($agendaDelDia) === 1 && $agendaDelDia[0]['localizador'] === $codigo,
    'El calendario del dia recupera la cita ficticia');
$agendaFuera = GestionEquipo::calendarioDesdeHasta('2020-01-01 00:00:00', '2020-01-02 00:00:00');
prueba($agendaFuera === [], 'El calendario no devuelve citas fuera del periodo');

prueba(GestionEquipo::asignar($incidenciaId, $tecnico, $cliente) === 'sin_permiso',
    'Un cliente no puede asignar una reparacion');
prueba(GestionEquipo::asignar($incidenciaId, $tecnicoAjeno, $admin) === 'tecnico_no_valido',
    'El administrador no puede asignar una especialidad incompatible');
prueba(GestionEquipo::asignar($incidenciaId, $tecnico, $admin) === 'asignada',
    'Un administrador asigna un tecnico valido');
prueba(GestionEquipo::asignar($incidenciaId, $tecnico, $admin) === 'estado_no_permitido',
    'No se permite la segunda asignacion');
prueba(GestionEquipo::incidenciaDeTecnico($incidenciaId, $tecnicoAjenoUsuario) === null,
    'Un tecnico distinto no puede consultar el detalle');
prueba(GestionEquipo::incidenciaDeTecnico($incidenciaId, $tecnicoUsuario) !== null,
    'El tecnico asignado ve su detalle');
prueba(count(GestionEquipo::incidenciasDeTecnico($tecnicoUsuario)) === 1,
    'El listado privado contiene su unica reparacion');
prueba(GestionEquipo::avanzarEstado($incidenciaId, $tecnicoAjenoUsuario, 'En curso') === 'no_encontrada',
    'Otro tecnico no puede cambiar un estado ajeno');
prueba(GestionEquipo::avanzarEstado($incidenciaId, $tecnicoUsuario, 'Finalizada') === 'estado_no_permitido',
    'Prohibido finalizar directamente desde Asignada');
prueba(GestionEquipo::avanzarEstado($incidenciaId, $tecnicoUsuario, 'En curso') === 'actualizada',
    'Asignada a En curso');
prueba(GestionEquipo::avanzarEstado($incidenciaId, $tecnicoUsuario, 'Finalizada') === 'actualizada',
    'En curso a Finalizada');
prueba(GestionEquipo::avanzarEstado($incidenciaId, $tecnicoUsuario, 'Finalizada') === 'estado_no_permitido',
    'No se puede finalizar dos veces');
$estado = $pdo->prepare('SELECT estado FROM incidencias WHERE id = :id');
$estado->execute(['id' => $incidenciaId]);
prueba($estado->fetchColumn() === 'Finalizada', 'El estado final persiste en MySQL');
$eventos = Solicitud::historial($incidenciaId);
prueba(count($eventos) === 4 && array_column($eventos, 'estado_nuevo') ===
    ['Pendiente', 'Asignada', 'En curso', 'Finalizada'],
    'Historial completo y ordenado, incluido el evento inicial');
prueba((string) $eventos[1]['actor'] === 'Persona ficticia admin'
    && (string) $eventos[2]['actor'] === 'Persona ficticia tecnico',
    'La autoria de administrador y tecnico se conserva en el historial');

// Render sin servidor web ni sesiones: comprueba las tres vistas en la BD de ensayo.
$zona = new DateTimeZone('Europe/Madrid');
$fechaSeleccionada = new DateTimeImmutable($fechaPrueba, $zona);
foreach (['mes', 'semana', 'dia'] as $vista) {
    if ($vista === 'mes') {
        $primero = $fechaSeleccionada->modify('first day of this month');
        $inicio = $primero->modify('-' . ((int) $primero->format('N') - 1) . ' days');
        $fin = $inicio->modify('+42 days');
    } elseif ($vista === 'semana') {
        $inicio = $fechaSeleccionada->modify('-' . ((int) $fechaSeleccionada->format('N') - 1) . ' days');
        $fin = $inicio->modify('+7 days');
    } else {
        $inicio = $fechaSeleccionada;
        $fin = $inicio->modify('+1 day');
    }
    $incidencias = GestionEquipo::calendarioDesdeHasta(
        $inicio->format('Y-m-d 00:00:00'), $fin->format('Y-m-d 00:00:00')
    );
    ob_start();
    require dirname(__DIR__) . '/app/Views/admin-calendario.php';
    $html = ob_get_clean();
    prueba(str_contains($html, 'Calendario de reparaciones') && str_contains($html, $codigo),
        'Vista de calendario ' . $vista . ' renderiza la reparacion');
}

echo 'TOTAL_PRUEBAS_INTEGRACION=' . $pases . '/20' . PHP_EOL;
