<?php
declare(strict_types=1);

// HTTP real a un servidor PHP local, dentro de la red aislada del MySQL temporal.
if (getenv('INTEGRATION_TEST_EPHEMERAL') !== '1'
    || getenv('DB_NAME') !== 'uoc_p2_integracion_prueba'
    || getenv('DB_HOST') !== '127.0.0.1') {
    fwrite(STDERR, "BLOQUEADO: solo entorno efimero de integracion.\n");
    exit(3);
}
require_once dirname(__DIR__) . '/app/Database/Conexion.php';

final class ClienteHttpPrueba
{
    private string $cookie = '';

    public function pedir(string $ruta, array $datos = [] , string $metodo = 'GET'): array
    {
        $cabeceras = ['Content-Type: application/x-www-form-urlencoded'];
        if ($this->cookie !== '') { $cabeceras[] = 'Cookie: ' . $this->cookie; }
        $opciones = [
            'http' => [
                'method' => $metodo,
                'header' => implode("\r\n", $cabeceras),
                'content' => $metodo === 'POST' ? http_build_query($datos) : '',
                'ignore_errors' => true, 'follow_location' => 0, 'timeout' => 4,
            ],
        ];
        $cabecerasRespuesta = [];
        $cuerpo = false;
        for ($intento = 0; $intento < 12; $intento++) {
            $cuerpo = @file_get_contents('http://127.0.0.1:8000' . $ruta, false,
                stream_context_create($opciones));
            $cabecerasRespuesta = $http_response_header ?? [];
            if ($cuerpo !== false) { break; }
            usleep(150000);
        }
        if ($cuerpo === false) { throw new RuntimeException('El servidor HTTP temporal no responde.'); }
        $codigo = 0;
        $destino = '';
        foreach ($cabecerasRespuesta as $cabecera) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $cabecera, $m)) { $codigo = (int) $m[1]; }
            if (preg_match('/^Set-Cookie:\s*([^;]+)/i', $cabecera, $m)) {
                $this->cookie = $m[1];
            }
            if (preg_match('/^Location:\s*(.+)$/i', $cabecera, $m)) { $destino = trim($m[1]); }
        }
        return ['codigo' => $codigo, 'cuerpo' => $cuerpo, 'destino' => $destino];
    }

    public function iniciarSesion(string $correo, string $clave): void
    {
        $login = $this->pedir('/login');
        if (!preg_match('/name="_csrf" value="([a-f0-9]{64})"/', $login['cuerpo'], $m)) {
            throw new RuntimeException('No existe token de login.');
        }
        $respuesta = $this->pedir('/login', [
            '_csrf' => $m[1], 'correo' => $correo, 'clave' => $clave,
        ], 'POST');
        if ($respuesta['codigo'] !== 303) { throw new RuntimeException('No se pudo iniciar sesion de ensayo.'); }
    }
}

$pases = 0;
function verificar(bool $condicion, string $mensaje): void
{
    global $pases;
    if (!$condicion) { throw new RuntimeException('FAIL HTTP: ' . $mensaje); }
    $pases++;
    echo 'PASS HTTP: ' . $mensaje . PHP_EOL;
}

$pdo = Conexion::abrir();
$idAleatorio = bin2hex(random_bytes(4));
$clave = 'Ensayo-HTTP-12345';
$cuentas = [];
foreach (['admin', 'tecnico', 'particular'] as $rol) {
    $correo = $rol . '-http-' . $idAleatorio . '@example.test';
    $consulta = $pdo->prepare(
        'INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre,:email,:hash,:rol)'
    );
    $consulta->execute([
        'nombre' => 'Persona HTTP inventada ' . $rol, 'email' => $correo,
        'hash' => password_hash($clave, PASSWORD_DEFAULT), 'rol' => $rol,
    ]);
    $cuentas[$rol] = ['id' => (int) $pdo->lastInsertId(), 'correo' => $correo];
}
$servicio = (int) $pdo->query('SELECT id FROM especialidades ORDER BY id LIMIT 1')->fetchColumn();
$consulta = $pdo->prepare(
    'INSERT INTO tecnicos (usuario_id, nombre_completo, especialidad_id, disponible)
     VALUES (:usuario, :nombre, :servicio, 1)'
);
$consulta->execute([
    'usuario' => $cuentas['tecnico']['id'], 'nombre' => 'Tecnico HTTP de prueba', 'servicio' => $servicio,
]);
$tecnicoId = (int) $pdo->lastInsertId();
$codigo = 'REP-2026-' . strtoupper(bin2hex(random_bytes(4)));
$fechaServicio = (new DateTimeImmutable('+8 days'))->format('Y-m-d 09:00:00');
$consulta = $pdo->prepare(
    "INSERT INTO incidencias (localizador, cliente_id, especialidad_id, descripcion,
         direccion, fecha_servicio, franja_horaria, telefono_contacto, tipo_urgencia, estado)
     VALUES (:codigo,:cliente,:servicio,'Prueba funcional de reparacion ficticia',
         'Avenida Inventada 8',:fecha,'manana','600000000','Estándar','Pendiente')"
);
$consulta->execute([
    'codigo' => $codigo, 'cliente' => $cuentas['particular']['id'],
    'servicio' => $servicio, 'fecha' => $fechaServicio,
]);
$incidenciaId = (int) $pdo->lastInsertId();
$consulta = $pdo->prepare(
    "INSERT INTO historial_estados (incidencia_id,estado_anterior,estado_nuevo,actor_id)
     VALUES (:id,NULL,'Pendiente',:cliente)"
);
$consulta->execute(['id' => $incidenciaId, 'cliente' => $cuentas['particular']['id']]);

$anonimo = new ClienteHttpPrueba();
verificar($anonimo->pedir('/admin')['codigo'] === 302, 'El panel admin exige login');
verificar($anonimo->pedir('/tecnico')['codigo'] === 302, 'El panel tecnico exige login');

$admin = new ClienteHttpPrueba();
$admin->iniciarSesion($cuentas['admin']['correo'], $clave);
$panel = $admin->pedir('/admin');
verificar($panel['codigo'] === 200 && str_contains($panel['cuerpo'], $codigo),
    'El admin ve la incidencia de prueba');
verificar(str_contains($panel['cuerpo'], 'Tecnico HTTP de prueba'), 'El admin ve su tecnico compatible');
preg_match('/name="_csrf" value="([a-f0-9]{64})"/', $panel['cuerpo'], $coincidencias);
$tokenAdmin = $coincidencias[1] ?? '';
verificar($admin->pedir('/admin/asignar', [
    '_csrf' => 'token-invalido', 'incidencia_id' => $incidenciaId, 'tecnico_id' => $tecnicoId,
], 'POST')['codigo'] === 403, 'La asignacion rechaza CSRF invalido');
$asignada = $admin->pedir('/admin/asignar', [
    '_csrf' => $tokenAdmin, 'incidencia_id' => $incidenciaId, 'tecnico_id' => $tecnicoId,
], 'POST');
verificar($asignada['codigo'] === 303, 'La asignacion correcta devuelve 303');
$fecha = substr($fechaServicio, 0, 10);
foreach (['mes', 'semana', 'dia'] as $vista) {
    $calendario = $admin->pedir('/admin/calendario?vista=' . $vista . '&fecha=' . $fecha);
    verificar($calendario['codigo'] === 200 && str_contains($calendario['cuerpo'], $codigo),
        'Ruta HTTP calendario ' . $vista);
}
verificar($admin->pedir('/admin/calendario?fecha=fecha-invalida')['codigo'] === 422,
    'El calendario rechaza fecha malformada');

$cliente = new ClienteHttpPrueba();
$cliente->iniciarSesion($cuentas['particular']['correo'], $clave);
verificar($cliente->pedir('/admin')['codigo'] === 403, 'Un cliente no puede usar el panel admin');
verificar($cliente->pedir('/tecnico')['codigo'] === 403, 'Un cliente no puede usar el panel tecnico');

$tecnico = new ClienteHttpPrueba();
$tecnico->iniciarSesion($cuentas['tecnico']['correo'], $clave);
$listado = $tecnico->pedir('/tecnico');
verificar($listado['codigo'] === 200 && str_contains($listado['cuerpo'], $codigo),
    'El tecnico ve sus reparaciones');
$detalle = $tecnico->pedir('/tecnico/aviso?id=' . $incidenciaId);
verificar($detalle['codigo'] === 200 && str_contains($detalle['cuerpo'], 'Historial de estados'),
    'El detalle muestra historial');
preg_match('/name="_csrf" value="([a-f0-9]{64})"/', $detalle['cuerpo'], $coincidencias);
$tokenTecnico = $coincidencias[1] ?? '';
$datos = ['incidencia_id' => $incidenciaId, 'nuevo_estado' => 'Finalizada', '_csrf' => $tokenTecnico];
verificar($tecnico->pedir('/tecnico/aviso/estado', $datos, 'POST')['codigo'] === 409,
    'No se puede finalizar directamente por HTTP');
$datos['_csrf'] = 'incorrecto';
$datos['nuevo_estado'] = 'En curso';
verificar($tecnico->pedir('/tecnico/aviso/estado', $datos, 'POST')['codigo'] === 403,
    'El avance de tecnico rechaza CSRF invalido');
$datos['_csrf'] = $tokenTecnico;
verificar($tecnico->pedir('/tecnico/aviso/estado', $datos, 'POST')['codigo'] === 303,
    'El tecnico pasa a En curso por HTTP');
$datos['nuevo_estado'] = 'Finalizada';
verificar($tecnico->pedir('/tecnico/aviso/estado', $datos, 'POST')['codigo'] === 303,
    'El tecnico finaliza por HTTP');
$detalleFinal = $tecnico->pedir('/tecnico/aviso?id=' . $incidenciaId);
verificar($detalleFinal['codigo'] === 200 && str_contains($detalleFinal['cuerpo'], 'Finalizada'),
    'El detalle refleja el estado final');
$consulta = $pdo->prepare('SELECT COUNT(*) FROM historial_estados WHERE incidencia_id = :id');
$consulta->execute(['id' => $incidenciaId]);
verificar((int) $consulta->fetchColumn() === 4, 'El historial contiene cuatro eventos en MySQL');

echo 'TOTAL_PRUEBAS_HTTP=' . $pases . '/20' . PHP_EOL;
