<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Models/Usuario.php';

final class UsuarioController
{
    public static function token(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function comprobarToken(): bool
    {
        $recibido = (string) ($_POST['_csrf'] ?? '');
        if ($recibido === '' || !hash_equals(self::token(), $recibido)) {
            $this->error(403, 'Formulario caducado o no autorizado. Recarga la página.');
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

    public function procesarRegistro(): void
    {
        if (!$this->comprobarToken()) {
            return;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $correo = mb_strtolower(trim((string) ($_POST['correo'] ?? '')), 'UTF-8');
        $clave = (string) ($_POST['clave'] ?? '');

        if ($nombre === '' || mb_strlen($nombre, 'UTF-8') > 100) {
            $this->error(422, 'El nombre debe tener entre 1 y 100 caracteres.');
            return;
        }
        if (mb_strlen($correo, 'UTF-8') > 100 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->error(422, 'El correo electrónico no es válido.');
            return;
        }
        // PASSWORD_DEFAULT usa bcrypt en PHP 8.2; más de 72 bytes se truncarían.
        if (strlen($clave) < 8 || strlen($clave) > 72) {
            $this->error(422, 'La contraseña debe tener entre 8 y 72 bytes.');
            return;
        }

        try {
            $hash = password_hash($clave, PASSWORD_DEFAULT);
            Usuario::crearParticular($nombre, $correo, $hash);
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $this->error(409, 'Este correo electrónico ya está registrado.');
                return;
            }
            $this->error(500, 'No se ha podido crear la cuenta.');
            return;
        }

        header('Location: /login?registro=ok', true, 303);
    }

    public function mostrarLogin(): void
    {
        $csrfToken = self::token();
        $registroCorrecto = ($_GET['registro'] ?? '') === 'ok';
        require dirname(__DIR__) . '/Views/login.php';
    }

    public function procesarLogin(): void
    {
        if (!$this->comprobarToken()) {
            return;
        }
        $correo = mb_strtolower(trim((string) ($_POST['correo'] ?? '')), 'UTF-8');
        $clave = (string) ($_POST['clave'] ?? '');

        try {
            $usuario = Usuario::buscarPorCorreo($correo);
        } catch (PDOException $ex) {
            $this->error(500, 'No se ha podido consultar la cuenta.');
            return;
        }

        if ($usuario === null || !password_verify($clave, $usuario['password'])) {
            $this->error(401, 'Correo o contraseña incorrectos.');
            return;
        }

        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $usuario['id'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: /perfil', true, 303);
    }

    public function mostrarPerfil(): void
    {
        $id = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($id < 1) {
            header('Location: /login', true, 302);
            return;
        }
        try {
            $usuario = Usuario::buscarPorId($id);
        } catch (PDOException $ex) {
            $this->error(500, 'No se ha podido cargar el perfil.');
            return;
        }
        if ($usuario === null) {
            unset($_SESSION['usuario_id']);
            header('Location: /login', true, 302);
            return;
        }
        $csrfToken = self::token();
        require dirname(__DIR__) . '/Views/perfil.php';
    }

    public function actualizarPerfil(): void
    {
        $id = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($id < 1) {
            $this->error(401, 'Inicia sesión para editar tu perfil.');
            return;
        }
        if (!$this->comprobarToken()) {
            return;
        }
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        if ($nombre === '' || mb_strlen($nombre, 'UTF-8') > 100 || mb_strlen($telefono, 'UTF-8') > 20) {
            $this->error(422, 'Comprueba el nombre (máximo 100 caracteres) y teléfono (máximo 20).');
            return;
        }
        try {
            Usuario::actualizarPerfil($id, $nombre, $telefono === '' ? null : $telefono);
        } catch (PDOException $ex) {
            $this->error(500, 'No se ha podido actualizar el perfil.');
            return;
        }
        header('Location: /perfil?guardado=si', true, 303);
    }

    public function actualizarCredenciales(): void
    {
        $id = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($id < 1) {
            $this->error(401, 'Inicia sesión para modificar el acceso.');
            return;
        }
        if (!$this->comprobarToken()) {
            return;
        }
        $correo = mb_strtolower(trim((string) ($_POST['correo'] ?? '')), 'UTF-8');
        $claveActual = (string) ($_POST['clave_actual'] ?? '');
        $claveNueva = (string) ($_POST['clave_nueva'] ?? '');
        if (mb_strlen($correo, 'UTF-8') > 100 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->error(422, 'El nuevo correo no es válido.');
            return;
        }
        if ($claveNueva !== '' && (strlen($claveNueva) < 8 || strlen($claveNueva) > 72)) {
            $this->error(422, 'La nueva contraseña debe tener entre 8 y 72 bytes.');
            return;
        }
        try {
            $usuario = Usuario::buscarCredencialesPorId($id);
            if ($usuario === null || !password_verify($claveActual, $usuario['password'])) {
                $this->error(403, 'La contraseña actual no es correcta.');
                return;
            }
            $nuevoHash = $claveNueva === '' ? $usuario['password'] : password_hash($claveNueva, PASSWORD_DEFAULT);
            Usuario::actualizarCredenciales($id, $correo, $nuevoHash);
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $this->error(409, 'Ese correo ya está registrado.');
                return;
            }
            $this->error(500, 'No se han podido modificar las credenciales.');
            return;
        }
        header('Location: /perfil?seguridad=si', true, 303);
    }

    public function salir(): void
    {
        if (!$this->comprobarToken()) {
            return;
        }
        $_SESSION = [];
        session_regenerate_id(true);
        header('Location: /login', true, 303);
    }
}
