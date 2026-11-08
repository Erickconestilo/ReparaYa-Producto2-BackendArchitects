<?php
declare(strict_types=1);

if (getenv('INTEGRATION_TEST_EPHEMERAL') !== '1'
    || getenv('DB_NAME') !== 'uoc_p2_integracion_prueba'
    || getenv('DB_HOST') !== '127.0.0.1') {
    fwrite(STDERR, "BLOQUEADO: solo base MySQL efimera de pruebas.\n");
    exit(3);
}
require_once dirname(__DIR__) . '/app/Models/CatalogoEquipo.php';
require_once dirname(__DIR__) . '/app/Models/GestionEquipo.php';
require_once dirname(__DIR__) . '/app/Models/Solicitud.php';

$pdo = Conexion::abrir();
$pases = 0;
function comprobarCatalogo(bool $resultado, string $mensaje): void
{
    global $pases;
    if (!$resultado) { throw new RuntimeException('FAIL CATALOGO: ' . $mensaje); }
    $pases++;
    echo 'PASS CATALOGO: ' . $mensaje . PHP_EOL;
}

$idAleatorio = bin2hex(random_bytes(5));
$servicioNombre = 'Servicio ficticio ' . $idAleatorio;
$servicioNuevo = 'Servicio editado ' . $idAleatorio;
$contrasena = 'EnsayoCatalogo-12345';
$correo = 'tecnico-' . $idAleatorio . '@example.test';

comprobarCatalogo(CatalogoEquipo::crearServicio('', '45.00') === 'datos_no_validos',
    'No se acepta servicio sin nombre');
comprobarCatalogo(CatalogoEquipo::crearServicio($servicioNombre, '-12') === 'datos_no_validos',
    'No se acepta precio negativo');
comprobarCatalogo(CatalogoEquipo::crearServicio($servicioNombre, '12.999') === 'datos_no_validos',
    'Se exigen maximo dos decimales');
comprobarCatalogo(CatalogoEquipo::crearServicio($servicioNombre, '120.25') === 'creado',
    'Nuevo servicio y precio creados');
comprobarCatalogo(CatalogoEquipo::crearServicio(mb_strtoupper($servicioNombre), '120.25') === 'duplicado',
    'No se duplica un nombre con distintas mayusculas');

$consulta = $pdo->prepare('SELECT id FROM especialidades WHERE nombre_especialidad = :nombre');
$consulta->execute(['nombre' => $servicioNombre]);
$servicioId = (int) $consulta->fetchColumn();
comprobarCatalogo($servicioId > 0 && Solicitud::existeServicio($servicioId),
    'Cliente ve el servicio activo');
comprobarCatalogo(CatalogoEquipo::actualizarServicio($servicioId, $servicioNuevo, '125.50', false)
    === 'actualizado', 'Servicio editable y desactivable');
comprobarCatalogo(!Solicitud::existeServicio($servicioId),
    'Servicio inactivo no admite nuevas solicitudes');
$consulta = $pdo->prepare('SELECT precio_base, activa FROM especialidades WHERE id = :id');
$consulta->execute(['id' => $servicioId]);
$servicio = $consulta->fetch();
comprobarCatalogo($servicio !== false && $servicio['precio_base'] === '125.50'
    && (int) $servicio['activa'] === 0, 'Precio y baja logica persistidos');
comprobarCatalogo(CatalogoEquipo::crearTecnico('Tecnico de ensayo', $correo,
    $contrasena, $servicioId) === 'especialidad_no_valida',
    'No se crean tecnicos para servicios desactivados');
comprobarCatalogo(CatalogoEquipo::actualizarServicio($servicioId, $servicioNuevo,
    '125.50', true) === 'actualizado' && Solicitud::existeServicio($servicioId),
    'Servicio reactivado');
comprobarCatalogo(CatalogoEquipo::actualizarServicio(987654321, 'Ficticio', '1.00', true)
    === 'no_encontrado', 'Editar servicio inexistente no lo crea');

comprobarCatalogo(CatalogoEquipo::crearTecnico('Tecnico de ensayo', $correo,
    $contrasena, $servicioId) === 'creado', 'Alta simultanea de usuario y perfil de tecnico');
$consulta = $pdo->prepare('SELECT u.id AS usuario_id, u.rol, u.password, t.id AS tecnico_id
                          FROM usuarios u JOIN tecnicos t ON t.usuario_id = u.id WHERE u.email = :correo');
$consulta->execute(['correo' => $correo]);
$tecnico = $consulta->fetch();
$usuarioTecnicoId = (int) ($tecnico['usuario_id'] ?? 0);
$tecnicoId = (int) ($tecnico['tecnico_id'] ?? 0);
comprobarCatalogo($usuarioTecnicoId > 0 && $tecnicoId > 0
    && $tecnico['rol'] === 'tecnico' && password_verify($contrasena, $tecnico['password']),
    'El rol y el hash de contrasena son correctos');
comprobarCatalogo(CatalogoEquipo::actualizarTecnico($tecnicoId, 'Tecnico editado',
    $servicioId, false) === 'actualizado', 'Tecnico sin tareas se puede desactivar');
$consulta = $pdo->prepare('SELECT u.nombre, t.disponible FROM tecnicos t JOIN usuarios u
                          ON u.id = t.usuario_id WHERE t.id = :id');
$consulta->execute(['id' => $tecnicoId]);
$editado = $consulta->fetch();
comprobarCatalogo($editado['nombre'] === 'Tecnico editado' && (int) $editado['disponible'] === 0,
    'Nombre y disponibilidad se actualizan en ambas tablas');
$demoEspecialidad = (int) $pdo->query('SELECT id FROM especialidades WHERE id <> ' . $servicioId
    . ' ORDER BY id LIMIT 1')->fetchColumn();
comprobarCatalogo(CatalogoEquipo::actualizarTecnico($tecnicoId, 'Tecnico reactivado',
    $demoEspecialidad, true) === 'actualizado',
    'Tecnico reactivado y especialidad modificada sin trabajos activos');
comprobarCatalogo(CatalogoEquipo::actualizarTecnico(987654321, 'Inexistente',
    $demoEspecialidad, false) === 'no_encontrado', 'Tecnico inexistente no se modifica');

// Una reparacion real de ensayo bloquea la baja y el cambio de especialidad.
$usuarios = [];
foreach (['admin', 'particular'] as $rol) {
    $consulta = $pdo->prepare("INSERT INTO usuarios (nombre,email,password,rol)
        VALUES (:nombre,:correo,:clave,:rol)");
    $consulta->execute([
        'nombre' => 'Persona de ensayo ' . $rol,
        'correo' => $rol . '-' . $idAleatorio . '@example.test',
        'clave' => password_hash('Ensayo-12345', PASSWORD_DEFAULT), 'rol' => $rol,
    ]);
    $usuarios[$rol] = (int) $pdo->lastInsertId();
}
$localizador = 'REP-2026-' . strtoupper(bin2hex(random_bytes(4)));
$consulta = $pdo->prepare("INSERT INTO incidencias (localizador, cliente_id, especialidad_id,
    descripcion, direccion, fecha_servicio, franja_horaria, telefono_contacto, tipo_urgencia, estado)
    VALUES (:codigo,:cliente,:servicio,'Averia inventada con historial','Calle de prueba 15',
    :fecha,'manana','600000000','Estándar','Pendiente')");
$consulta->execute([
    'codigo' => $localizador, 'cliente' => $usuarios['particular'],
    'servicio' => $demoEspecialidad,
    'fecha' => (new DateTimeImmutable('+9 days'))->format('Y-m-d 09:00:00'),
]);
$incidenciaId = (int) $pdo->lastInsertId();
$consulta = $pdo->prepare("INSERT INTO historial_estados (incidencia_id,estado_anterior,
    estado_nuevo,actor_id) VALUES (:id,NULL,'Pendiente',:cliente)");
$consulta->execute(['id' => $incidenciaId, 'cliente' => $usuarios['particular']]);
comprobarCatalogo(GestionEquipo::asignar($incidenciaId, $tecnicoId, $usuarios['admin']) === 'asignada',
    'Incidencia ficticia asignada al tecnico activo');
comprobarCatalogo(CatalogoEquipo::actualizarTecnico($tecnicoId, 'No desactivar',
    $demoEspecialidad, false) === 'con_reparaciones_activas',
    'No se desactiva un tecnico con incidencia asignada');
comprobarCatalogo(CatalogoEquipo::actualizarTecnico($tecnicoId, 'No cambiar',
    $servicioId, true) === 'con_reparaciones_activas',
    'No se cambia de especialidad con trabajo pendiente');
comprobarCatalogo(GestionEquipo::avanzarEstado($incidenciaId, $usuarioTecnicoId, 'En curso')
    === 'actualizada' && GestionEquipo::avanzarEstado($incidenciaId, $usuarioTecnicoId, 'Finalizada')
    === 'actualizada', 'Tecnico finaliza la reparacion antes de la baja');
comprobarCatalogo(CatalogoEquipo::actualizarTecnico($tecnicoId, 'Tecnico archivado',
    $demoEspecialidad, false) === 'actualizado',
    'Se permite desactivar despues de finalizar todas las tareas');
comprobarCatalogo(GestionEquipo::incidenciasDeTecnico($usuarioTecnicoId) === [],
    'El panel de un tecnico no disponible no muestra agenda activa');
comprobarCatalogo(Solicitud::obtenerPorCliente($incidenciaId, $usuarios['particular']) !== null
    && count(Solicitud::historial($incidenciaId)) === 4,
    'La baja logica conserva la incidencia y su trazabilidad');

echo 'TOTAL_PRUEBAS_CATALOGO=' . $pases . PHP_EOL;
