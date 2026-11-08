<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Database/Conexion.php';

/** Altas y cambios administrativos; las bajas son logicas para preservar historial. */
final class CatalogoEquipo
{
    private static function nombreValido(string $nombre, int $maximo): bool
    {
        return $nombre !== '' && mb_strlen($nombre, 'UTF-8') <= $maximo;
    }

    private static function precioValido(string $precio): bool
    {
        return preg_match('/^(0|[1-9][0-9]{0,7})(\.[0-9]{1,2})?$/D', $precio) === 1;
    }

    public static function servicios(): array
    {
        return Conexion::abrir()->query(
            'SELECT id, nombre_especialidad, precio_base, activa
             FROM especialidades ORDER BY nombre_especialidad, id'
        )->fetchAll();
    }

    public static function crearServicio(string $nombre, string $precio): string
    {
        $nombre = trim($nombre);
        if (!self::nombreValido($nombre, 50) || !self::precioValido($precio)) {
            return 'datos_no_validos';
        }
        $pdo = Conexion::abrir();
        $duplicado = $pdo->prepare(
            'SELECT 1 FROM especialidades WHERE LOWER(nombre_especialidad) = LOWER(:nombre) LIMIT 1'
        );
        $duplicado->execute(['nombre' => $nombre]);
        if ($duplicado->fetchColumn() !== false) { return 'duplicado'; }
        $consulta = $pdo->prepare(
            'INSERT INTO especialidades (nombre_especialidad, precio_base, activa)
             VALUES (:nombre, :precio, 1)'
        );
        $consulta->execute(['nombre' => $nombre, 'precio' => $precio]);
        return 'creado';
    }

    public static function actualizarServicio(int $id, string $nombre, string $precio, bool $activa): string
    {
        $nombre = trim($nombre);
        if ($id < 1 || !self::nombreValido($nombre, 50) || !self::precioValido($precio)) {
            return 'datos_no_validos';
        }
        $pdo = Conexion::abrir();
        $pdo->beginTransaction();
        try {
            $consulta = $pdo->prepare('SELECT id FROM especialidades WHERE id = :id FOR UPDATE');
            $consulta->execute(['id' => $id]);
            if ($consulta->fetchColumn() === false) {
                $pdo->rollBack();
                return 'no_encontrado';
            }
            $duplicado = $pdo->prepare(
                'SELECT 1 FROM especialidades WHERE LOWER(nombre_especialidad) = LOWER(:nombre)
                 AND id <> :id LIMIT 1'
            );
            $duplicado->execute(['nombre' => $nombre, 'id' => $id]);
            if ($duplicado->fetchColumn() !== false) {
                $pdo->rollBack();
                return 'duplicado';
            }
            $actualiza = $pdo->prepare(
                'UPDATE especialidades SET nombre_especialidad = :nombre,
                 precio_base = :precio, activa = :activa WHERE id = :id'
            );
            $actualiza->execute([
                'nombre' => $nombre, 'precio' => $precio, 'activa' => $activa ? 1 : 0, 'id' => $id,
            ]);
            $pdo->commit();
            return 'actualizado';
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            throw $error;
        }
    }

    public static function tecnicos(): array
    {
        return Conexion::abrir()->query(
            'SELECT t.id, t.usuario_id, t.nombre_completo, t.especialidad_id, t.disponible,
                    u.email, e.nombre_especialidad, e.activa AS especialidad_activa
             FROM tecnicos t
             JOIN usuarios u ON u.id = t.usuario_id
             LEFT JOIN especialidades e ON e.id = t.especialidad_id
             ORDER BY t.nombre_completo, t.id'
        )->fetchAll();
    }

    public static function crearTecnico(string $nombre, string $correo, string $clave, int $especialidadId): string
    {
        $nombre = trim($nombre);
        $correo = mb_strtolower(trim($correo), 'UTF-8');
        if (!self::nombreValido($nombre, 100) || strlen($correo) > 100
            || !filter_var($correo, FILTER_VALIDATE_EMAIL)
            || strlen($clave) < 8 || strlen($clave) > 72 || $especialidadId < 1) {
            return 'datos_no_validos';
        }
        $pdo = Conexion::abrir();
        $pdo->beginTransaction();
        try {
            $consulta = $pdo->prepare('SELECT 1 FROM especialidades WHERE id = :id AND activa = 1');
            $consulta->execute(['id' => $especialidadId]);
            if ($consulta->fetchColumn() === false) {
                $pdo->rollBack();
                return 'especialidad_no_valida';
            }
            $cuenta = $pdo->prepare(
                "INSERT INTO usuarios (nombre, email, password, rol)
                 VALUES (:nombre, :correo, :clave, 'tecnico')"
            );
            $cuenta->execute([
                'nombre' => $nombre, 'correo' => $correo,
                'clave' => password_hash($clave, PASSWORD_DEFAULT),
            ]);
            $usuarioId = (int) $pdo->lastInsertId();
            $perfil = $pdo->prepare(
                'INSERT INTO tecnicos (usuario_id, nombre_completo, especialidad_id, disponible)
                 VALUES (:usuario, :nombre, :especialidad, 1)'
            );
            $perfil->execute([
                'usuario' => $usuarioId, 'nombre' => $nombre, 'especialidad' => $especialidadId,
            ]);
            $pdo->commit();
            return 'creado';
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            throw $error;
        }
    }

    public static function actualizarTecnico(int $id, string $nombre, int $especialidadId, bool $disponible): string
    {
        $nombre = trim($nombre);
        if ($id < 1 || !self::nombreValido($nombre, 100) || $especialidadId < 1) {
            return 'datos_no_validos';
        }
        $pdo = Conexion::abrir();
        $pdo->beginTransaction();
        try {
            $consulta = $pdo->prepare(
                'SELECT usuario_id, especialidad_id, disponible FROM tecnicos WHERE id = :id FOR UPDATE'
            );
            $consulta->execute(['id' => $id]);
            $anterior = $consulta->fetch();
            if ($anterior === false) {
                $pdo->rollBack();
                return 'no_encontrado';
            }

            $especialidad = $pdo->prepare('SELECT 1 FROM especialidades WHERE id = :id AND activa = 1');
            $especialidad->execute(['id' => $especialidadId]);
            if ($especialidad->fetchColumn() === false) {
                $pdo->rollBack();
                return 'especialidad_no_valida';
            }
            if (!$disponible || (int) $anterior['especialidad_id'] !== $especialidadId) {
                $ocupadas = $pdo->prepare(
                    "SELECT COUNT(*) FROM incidencias WHERE tecnico_id = :tecnico
                     AND estado IN ('Asignada', 'En curso')"
                );
                $ocupadas->execute(['tecnico' => $id]);
                if ((int) $ocupadas->fetchColumn() > 0) {
                    $pdo->rollBack();
                    return 'con_reparaciones_activas';
                }
            }

            $cuenta = $pdo->prepare('UPDATE usuarios SET nombre = :nombre WHERE id = :usuario');
            $cuenta->execute(['nombre' => $nombre, 'usuario' => (int) $anterior['usuario_id']]);
            $perfil = $pdo->prepare(
                'UPDATE tecnicos SET nombre_completo = :nombre, especialidad_id = :especialidad,
                 disponible = :disponible WHERE id = :id'
            );
            $perfil->execute([
                'nombre' => $nombre, 'especialidad' => $especialidadId,
                'disponible' => $disponible ? 1 : 0, 'id' => $id,
            ]);
            $pdo->commit();
            return 'actualizado';
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            throw $error;
        }
    }
}
