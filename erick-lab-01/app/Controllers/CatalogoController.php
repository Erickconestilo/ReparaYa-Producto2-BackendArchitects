<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Models/CatalogoEquipo.php';
require_once dirname(__DIR__) . '/Models/Usuario.php';
require_once __DIR__ . '/UsuarioController.php';

/** Gestion de servicios y tecnicos: solo administradores, con CSRF y bajas logicas. */
final class CatalogoController
{
    private function requiereAdmin(): bool
    {
        $id = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($id < 1) {
            header('Location: /login', true, 302);
            return false;
        }
        $usuario = Usuario::buscarPorId($id);
        if ($usuario === null || $usuario['rol'] !== 'admin') {
            $this->error(403, 'Solo administracion puede gestionar el catalogo.');
            return false;
        }
        return true;
    }

    private function error(int $codigo, string $mensaje): void
    {
        http_response_code($codigo);
        header('Content-Type: text/plain; charset=utf-8');
        echo $mensaje;
    }

    private function csrfValido(): bool
    {
        $enviado = (string) ($_POST['_csrf'] ?? '');
        if ($enviado === '' || !hash_equals(UsuarioController::token(), $enviado)) {
            $this->error(403, 'Formulario no autorizado. Recarga la pagina.');
            return false;
        }
        return true;
    }

    private function booleanoFormulario(string $nombre): ?bool
    {
        $valor = (string) ($_POST[$nombre] ?? '0');
        if (!in_array($valor, ['0', '1'], true)) { return null; }
        return $valor === '1';
    }

    public function servicios(): void
    {
        if (!$this->requiereAdmin()) { return; }
        try {
            $servicios = CatalogoEquipo::servicios();
        } catch (Throwable $error) {
            $this->error(500, 'No se ha podido cargar el catalogo.');
            return;
        }
        $csrfToken = UsuarioController::token();
        require dirname(__DIR__) . '/Views/admin-servicios.php';
    }

    public function crearServicio(): void
    {
        if (!$this->requiereAdmin() || !$this->csrfValido()) { return; }
        $nombre = (string) ($_POST['nombre'] ?? '');
        $precio = str_replace(',', '.', trim((string) ($_POST['precio'] ?? '')));
        try {
            $resultado = CatalogoEquipo::crearServicio($nombre, $precio);
        } catch (Throwable $error) {
            $this->error(500, 'No se ha podido crear el servicio.');
            return;
        }
        if ($resultado === 'creado') { header('Location: /admin/servicios?creado=si', true, 303); }
        elseif ($resultado === 'duplicado') { $this->error(409, 'Ya existe una especialidad con ese nombre.'); }
        else { $this->error(422, 'Comprueba el nombre y el precio (maximo dos decimales).'); }
    }

    public function actualizarServicio(): void
    {
        if (!$this->requiereAdmin() || !$this->csrfValido()) { return; }
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $activa = $this->booleanoFormulario('activa');
        if (!is_int($id) || $id < 1 || $activa === null) {
            $this->error(422, 'Servicio o estado no validos.');
            return;
        }
        try {
            $resultado = CatalogoEquipo::actualizarServicio(
                $id, (string) ($_POST['nombre'] ?? ''),
                str_replace(',', '.', trim((string) ($_POST['precio'] ?? ''))), $activa
            );
        } catch (Throwable $error) {
            $this->error(500, 'No se ha podido actualizar el servicio.');
            return;
        }
        if ($resultado === 'actualizado') { header('Location: /admin/servicios?actualizado=si', true, 303); }
        elseif ($resultado === 'no_encontrado') { $this->error(404, 'Servicio no encontrado.'); }
        elseif ($resultado === 'duplicado') { $this->error(409, 'Ya existe otro servicio con ese nombre.'); }
        else { $this->error(422, 'Datos del servicio no validos.'); }
    }

    public function tecnicos(): void
    {
        if (!$this->requiereAdmin()) { return; }
        try {
            $tecnicos = CatalogoEquipo::tecnicos();
            $especialidades = CatalogoEquipo::servicios();
        } catch (Throwable $error) {
            $this->error(500, 'No se han podido cargar los tecnicos.');
            return;
        }
        $csrfToken = UsuarioController::token();
        require dirname(__DIR__) . '/Views/admin-tecnicos.php';
    }

    public function crearTecnico(): void
    {
        if (!$this->requiereAdmin() || !$this->csrfValido()) { return; }
        $especialidadId = filter_var($_POST['especialidad_id'] ?? null, FILTER_VALIDATE_INT);
        if (!is_int($especialidadId) || $especialidadId < 1) {
            $this->error(422, 'Selecciona una especialidad valida.');
            return;
        }
        try {
            $resultado = CatalogoEquipo::crearTecnico(
                (string) ($_POST['nombre'] ?? ''),
                (string) ($_POST['correo'] ?? ''),
                (string) ($_POST['clave'] ?? ''), $especialidadId
            );
        } catch (PDOException $error) {
            $this->error($error->getCode() === '23000' ? 409 : 500,
                $error->getCode() === '23000' ? 'El correo ya pertenece a otra cuenta.' : 'No se ha podido crear el tecnico.');
            return;
        }
        if ($resultado === 'creado') { header('Location: /admin/tecnicos?creado=si', true, 303); }
        elseif ($resultado === 'especialidad_no_valida') { $this->error(409, 'La especialidad esta desactivada o no existe.'); }
        else { $this->error(422, 'Comprueba nombre, correo, contrasena (8-72 bytes) y especialidad.'); }
    }

    public function actualizarTecnico(): void
    {
        if (!$this->requiereAdmin() || !$this->csrfValido()) { return; }
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $especialidadId = filter_var($_POST['especialidad_id'] ?? null, FILTER_VALIDATE_INT);
        $disponible = $this->booleanoFormulario('disponible');
        if (!is_int($id) || $id < 1 || !is_int($especialidadId) || $especialidadId < 1
            || $disponible === null) {
            $this->error(422, 'Datos del tecnico no validos.');
            return;
        }
        try {
            $resultado = CatalogoEquipo::actualizarTecnico(
                $id, (string) ($_POST['nombre'] ?? ''), $especialidadId, $disponible
            );
        } catch (Throwable $error) {
            $this->error(500, 'No se ha podido actualizar el tecnico.');
            return;
        }
        if ($resultado === 'actualizado') { header('Location: /admin/tecnicos?actualizado=si', true, 303); }
        elseif ($resultado === 'no_encontrado') { $this->error(404, 'Tecnico no encontrado.'); }
        elseif ($resultado === 'con_reparaciones_activas') {
            $this->error(409, 'Tiene reparaciones asignadas o en curso. Finalizalas antes de desactivarlo o cambiar su especialidad.');
        } elseif ($resultado === 'especialidad_no_valida') { $this->error(409, 'Especialidad desactivada o no encontrada.'); }
        else { $this->error(422, 'Nombre o especialidad no validos.'); }
    }
}
