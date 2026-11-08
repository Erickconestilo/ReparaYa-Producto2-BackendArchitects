<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Models/GestionEquipo.php';
require_once dirname(__DIR__) . '/Models/Usuario.php';
require_once dirname(__DIR__) . '/Models/Solicitud.php';
require_once __DIR__ . '/UsuarioController.php';

/** Integracion local de los modulos de administracion y tecnicos de P2. */
final class EquipoController
{
    private function usuarioConRol(string $rol): int
    {
        $id = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($id < 1) {
            header('Location: /login', true, 302);
            return 0;
        }
        $usuario = Usuario::buscarPorId($id);
        if ($usuario === null || $usuario['rol'] !== $rol) {
            $this->error(403, 'No tienes permiso para acceder a este apartado.');
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

    private function comprobarToken(): bool
    {
        $token = (string) ($_POST['_csrf'] ?? '');
        if ($token === '' || !hash_equals(UsuarioController::token(), $token)) {
            $this->error(403, 'Formulario no autorizado. Recarga la pagina.');
            return false;
        }
        return true;
    }

    public function administrar(): void
    {
        if ($this->usuarioConRol('admin') < 1) { return; }
        $estadoFiltro = (string) ($_GET['estado'] ?? '');
        $estadosValidos = ['Pendiente', 'Asignada', 'En curso', 'Finalizada', 'Cancelada'];
        if ($estadoFiltro !== '' && !in_array($estadoFiltro, $estadosValidos, true)) {
            $this->error(422, 'Estado de filtro no válido.');
            return;
        }
        try {
            $incidencias = GestionEquipo::incidenciasParaAdmin($estadoFiltro === '' ? null : $estadoFiltro);
            $tecnicos = GestionEquipo::tecnicosDisponibles();
        } catch (Throwable $error) {
            $this->error(500, 'No se ha podido cargar el panel de administración.');
            return;
        }
        $csrfToken = UsuarioController::token();
        require dirname(__DIR__) . '/Views/admin-incidencias.php';
    }

    public function asignar(): void
    {
        $adminId = $this->usuarioConRol('admin');
        if ($adminId < 1 || !$this->comprobarToken()) { return; }
        $incidenciaId = filter_var($_POST['incidencia_id'] ?? null, FILTER_VALIDATE_INT);
        $tecnicoId = filter_var($_POST['tecnico_id'] ?? null, FILTER_VALIDATE_INT);
        if (!is_int($incidenciaId) || $incidenciaId < 1 || !is_int($tecnicoId) || $tecnicoId < 1) {
            $this->error(422, 'Selecciona una incidencia y un tecnico validos.');
            return;
        }
        try {
            $resultado = GestionEquipo::asignar($incidenciaId, $tecnicoId, $adminId);
        } catch (Throwable $error) {
            $this->error(500, 'No se ha podido asignar el tecnico.');
            return;
        }
        if ($resultado === 'asignada') {
            header('Location: /admin?asignada=si', true, 303);
        } elseif ($resultado === 'no_encontrada') {
            $this->error(404, 'Incidencia no encontrada.');
        } elseif ($resultado === 'sin_permiso') {
            $this->error(403, 'No tienes permisos de administrador.');
        } else {
            $this->error(409, 'No se puede asignar: comprueba el estado, la disponibilidad y la especialidad del tecnico.');
        }
    }

    public function calendario(): void
    {
        if ($this->usuarioConRol('admin') < 1) { return; }
        $vista = (string) ($_GET['vista'] ?? 'mes');
        if (!in_array($vista, ['mes', 'semana', 'dia'], true)) { $vista = 'mes'; }
        $zona = new DateTimeZone('Europe/Madrid');
        $fechaTexto = (string) ($_GET['fecha'] ?? (new DateTimeImmutable('today', $zona))->format('Y-m-d'));
        $fechaSeleccionada = DateTimeImmutable::createFromFormat('!Y-m-d', $fechaTexto, $zona);
        if ($fechaSeleccionada === false || $fechaSeleccionada->format('Y-m-d') !== $fechaTexto) {
            $this->error(422, 'Fecha no valida. Usa AAAA-MM-DD.');
            return;
        }

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

        try {
            $incidencias = GestionEquipo::calendarioDesdeHasta(
                $inicio->format('Y-m-d 00:00:00'), $fin->format('Y-m-d 00:00:00')
            );
        } catch (Throwable $error) {
            $this->error(500, 'No se ha podido consultar el calendario.');
            return;
        }
        require dirname(__DIR__) . '/Views/admin-calendario.php';
    }

    public function misIncidenciasTecnico(): void
    {
        $usuarioId = $this->usuarioConRol('tecnico');
        if ($usuarioId < 1) { return; }
        try {
            $incidencias = GestionEquipo::incidenciasDeTecnico($usuarioId);
        } catch (Throwable $error) {
            $this->error(500, 'No se han podido cargar tus reparaciones.');
            return;
        }
        require dirname(__DIR__) . '/Views/tecnico-incidencias.php';
    }

    public function detalleTecnico(): void
    {
        $usuarioId = $this->usuarioConRol('tecnico');
        if ($usuarioId < 1) { return; }
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!is_int($id) || $id < 1) {
            $this->error(404, 'Reparacion no encontrada.');
            return;
        }
        try {
            $incidencia = GestionEquipo::incidenciaDeTecnico($id, $usuarioId);
            if ($incidencia === null) {
                $this->error(404, 'Reparacion no encontrada o no asignada a tu cuenta.');
                return;
            }
            $historial = Solicitud::historial($id);
        } catch (Throwable $error) {
            $this->error(500, 'No se ha podido consultar la reparacion.');
            return;
        }
        $csrfToken = UsuarioController::token();
        require dirname(__DIR__) . '/Views/tecnico-detalle.php';
    }

    public function avanzarEstado(): void
    {
        $usuarioId = $this->usuarioConRol('tecnico');
        if ($usuarioId < 1 || !$this->comprobarToken()) { return; }
        $id = filter_var($_POST['incidencia_id'] ?? null, FILTER_VALIDATE_INT);
        $estado = (string) ($_POST['nuevo_estado'] ?? '');
        if (!is_int($id) || $id < 1 || !in_array($estado, ['En curso', 'Finalizada'], true)) {
            $this->error(422, 'Estado o reparacion no validos.');
            return;
        }
        try {
            $resultado = GestionEquipo::avanzarEstado($id, $usuarioId, $estado);
        } catch (Throwable $error) {
            $this->error(500, 'No se ha podido modificar el estado.');
            return;
        }
        if ($resultado === 'actualizada') {
            header('Location: /tecnico/aviso?id=' . $id . '&estado=ok', true, 303);
        } elseif ($resultado === 'no_encontrada') {
            $this->error(404, 'Reparacion no asignada a tu cuenta.');
        } else {
            $this->error(409, 'Transicion no permitida. Primero En curso y despues Finalizada.');
        }
    }
}
