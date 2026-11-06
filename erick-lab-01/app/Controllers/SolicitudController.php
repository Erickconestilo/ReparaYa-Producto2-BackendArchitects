<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Models/Solicitud.php';
require_once dirname(__DIR__) . '/Models/Usuario.php';

final class SolicitudController
{
    private function clienteId(): int
    {
        $id = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($id < 1) {
            header('Location: /login', true, 302);
            return 0;
        }
        $usuario = Usuario::buscarPorId($id);
        if ($usuario === null || $usuario['rol'] !== 'particular') {
            http_response_code(403);
            echo 'Este formulario corresponde a clientes particulares.';
            return 0;
        }
        return $id;
    }

    private function error(int $codigo, string $mensaje): void
    {
        http_response_code($codigo);
        header('Content-Type: text/plain; charset=utf-8');
        echo $mensaje;
    }

    private function csrfCorrecto(): bool
    {
        $enviado = (string) ($_POST['_csrf'] ?? '');
        if ($enviado === '' || !hash_equals(UsuarioController::token(), $enviado)) {
            $this->error(403, 'Formulario no autorizado. Recarga la página.');
            return false;
        }
        return true;
    }

    public function formulario(): void
    {
        if ($this->clienteId() < 1) { return; }
        $servicios = Solicitud::servicios();
        $csrfToken = UsuarioController::token();
        require dirname(__DIR__) . '/Views/solicitar.php';
    }

    public function crear(): void
    {
        $cliente = $this->clienteId();
        if ($cliente < 1 || !$this->csrfCorrecto()) { return; }
        $servicio = filter_var($_POST['servicio'] ?? null, FILTER_VALIDATE_INT);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        $direccion = trim((string) ($_POST['direccion'] ?? ''));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $fecha = (string) ($_POST['fecha'] ?? '');
        $franja = (string) ($_POST['franja'] ?? '');
        $urgencia = (string) ($_POST['urgencia'] ?? '');

        if ($servicio === false || $servicio < 1 || !Solicitud::existeServicio($servicio)) {
            $this->error(422, 'Selecciona un servicio existente.'); return;
        }
        if (mb_strlen($descripcion, 'UTF-8') < 10 || mb_strlen($descripcion, 'UTF-8') > 2000
            || mb_strlen($direccion, 'UTF-8') < 5 || mb_strlen($direccion, 'UTF-8') > 255
            || mb_strlen($telefono, 'UTF-8') < 6 || mb_strlen($telefono, 'UTF-8') > 20) {
            $this->error(422, 'Comprueba la descripción, la dirección y el teléfono de contacto.'); return;
        }
        if (!in_array($franja, ['manana', 'tarde'], true) || !in_array($urgencia, ['Estándar', 'Urgente'], true)) {
            $this->error(422, 'Franja o tipo de urgencia no válido.'); return;
        }

        // Una fecha + franja se traduce a hora fija de comienzo (09:00/16:00).
        $hora = $franja === 'manana' ? '09:00' : '16:00';
        $zona = new DateTimeZone('Europe/Madrid');
        $cita = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $fecha . ' ' . $hora, $zona);
        if ($cita === false || $cita->format('Y-m-d') !== $fecha) {
            $this->error(422, 'La fecha seleccionada no es válida.'); return;
        }
        $segundos = $cita->getTimestamp() - (new DateTimeImmutable('now', $zona))->getTimestamp();
        if ($segundos <= 0) {
            $this->error(422, 'No se admiten citas en el pasado.'); return;
        }
        if ($urgencia === 'Estándar' && $segundos < 48 * 3600) {
            $this->error(422, 'Las solicitudes estándar necesitan al menos 48 horas de antelación.'); return;
        }

        try {
            $codigo = Solicitud::crear($cliente, $servicio, $descripcion, $direccion,
                $cita->format('Y-m-d H:i:s'), $franja, $telefono, $urgencia);
        } catch (Throwable $ex) {
            $this->error(500, 'No se ha podido guardar la solicitud.'); return;
        }
        header('Location: /mis-avisos?creada=' . rawurlencode($codigo), true, 303);
    }

    public function misAvisos(): void
    {
        $cliente = $this->clienteId();
        if ($cliente < 1) { return; }
        $avisos = Solicitud::listarPorCliente($cliente);
        require dirname(__DIR__) . '/Views/mis-avisos.php';
    }

    public function detalle(): void
    {
        $cliente = $this->clienteId();
        if ($cliente < 1) { return; }
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) { $this->error(404, 'Solicitud no encontrada.'); return; }
        $aviso = Solicitud::obtenerPorCliente($id, $cliente);
        if ($aviso === null) { $this->error(404, 'Solicitud no encontrada.'); return; }
        $historial = Solicitud::historial($id);
        $csrfToken = UsuarioController::token();
        $cita = new DateTimeImmutable($aviso['fecha_servicio'], new DateTimeZone('Europe/Madrid'));
        $puedeEditar = $aviso['estado'] === 'Pendiente'
            && $cita->getTimestamp() - (new DateTimeImmutable('now', new DateTimeZone('Europe/Madrid')))->getTimestamp() > 48 * 3600;
        require dirname(__DIR__) . '/Views/detalle-aviso.php';
    }

    public function editarFormulario(): void
    {
        $cliente = $this->clienteId();
        if ($cliente < 1) { return; }
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) { $this->error(404, 'Solicitud no encontrada.'); return; }
        $aviso = Solicitud::obtenerPorCliente($id, $cliente);
        if ($aviso === null) { $this->error(404, 'Solicitud no encontrada.'); return; }
        $cita = new DateTimeImmutable($aviso['fecha_servicio'], new DateTimeZone('Europe/Madrid'));
        if ($aviso['estado'] !== 'Pendiente'
            || $cita->getTimestamp() - (new DateTimeImmutable('now', new DateTimeZone('Europe/Madrid')))->getTimestamp() <= 48 * 3600) {
            $this->error(409, 'Solo puedes editar solicitudes pendientes con más de 48 horas de antelación.');
            return;
        }
        $csrfToken = UsuarioController::token();
        require dirname(__DIR__) . '/Views/editar-aviso.php';
    }

    public function editar(): void
    {
        $cliente = $this->clienteId();
        if ($cliente < 1 || !$this->csrfCorrecto()) { return; }
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) { $this->error(404, 'Solicitud no encontrada.'); return; }
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        $direccion = trim((string) ($_POST['direccion'] ?? ''));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        if (mb_strlen($descripcion, 'UTF-8') < 10 || mb_strlen($descripcion, 'UTF-8') > 2000
            || mb_strlen($direccion, 'UTF-8') < 5 || mb_strlen($direccion, 'UTF-8') > 255
            || mb_strlen($telefono, 'UTF-8') < 6 || mb_strlen($telefono, 'UTF-8') > 20) {
            $this->error(422, 'Comprueba la avería, la dirección y el teléfono de contacto.');
            return;
        }
        try {
            $resultado = Solicitud::actualizarDatosCliente($id, $cliente, $descripcion, $direccion,
                $telefono, new DateTimeImmutable('now', new DateTimeZone('Europe/Madrid')));
        } catch (Throwable $ex) {
            $this->error(500, 'No se ha podido actualizar la solicitud.');
            return;
        }
        if ($resultado === 'no_encontrada') { $this->error(404, 'Solicitud no encontrada.'); return; }
        if ($resultado !== 'actualizada' && $resultado !== 'sin_cambios') {
            $this->error(409, 'La solicitud ya no se puede editar por estado o plazo.');
            return;
        }
        header('Location: /aviso?id=' . $id . '&actualizado=si', true, 303);
    }

    public function cancelar(): void
    {
        $cliente = $this->clienteId();
        if ($cliente < 1 || !$this->csrfCorrecto()) { return; }
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) { $this->error(404, 'Solicitud no encontrada.'); return; }
        try {
            $resultado = Solicitud::cancelarCliente($id, $cliente,
                new DateTimeImmutable('now', new DateTimeZone('Europe/Madrid')));
        } catch (Throwable $ex) {
            $this->error(500, 'No se ha podido cancelar.'); return;
        }
        if ($resultado === 'no_encontrada') { $this->error(404, 'Solicitud no encontrada.'); return; }
        if ($resultado !== 'cancelada') { $this->error(409, 'No se permite cancelar por el estado o por faltar 48 horas o menos.'); return; }
        header('Location: /aviso?id=' . $id . '&cancelada=si', true, 303);
    }
}
