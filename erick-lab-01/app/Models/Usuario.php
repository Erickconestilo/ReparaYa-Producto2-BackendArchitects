<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Database/Conexion.php';

// Modelo: aquí viven las consultas SQL, nunca en una vista HTML.
final class Usuario
{
    public static function buscarPorCorreo(string $correo): ?array
    {
        $consulta = Conexion::abrir()->prepare(
            'SELECT id, nombre, email, password, rol, telefono FROM usuarios WHERE email = :correo LIMIT 1'
        );
        $consulta->execute(['correo' => $correo]);
        $fila = $consulta->fetch();
        return $fila === false ? null : $fila;
    }

    public static function buscarPorId(int $id): ?array
    {
        $consulta = Conexion::abrir()->prepare(
            'SELECT id, nombre, email, rol, telefono, created_at FROM usuarios WHERE id = :id LIMIT 1'
        );
        $consulta->execute(['id' => $id]);
        $fila = $consulta->fetch();
        return $fila === false ? null : $fila;
    }

    public static function buscarCredencialesPorId(int $id): ?array
    {
        $consulta = Conexion::abrir()->prepare(
            'SELECT id, email, password FROM usuarios WHERE id = :id LIMIT 1'
        );
        $consulta->execute(['id' => $id]);
        $fila = $consulta->fetch();
        return $fila === false ? null : $fila;
    }

    public static function crearParticular(string $nombre, string $correo, string $hash): int
    {
        $pdo = Conexion::abrir();
        $consulta = $pdo->prepare(
            "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :correo, :hash, 'particular')"
        );
        $consulta->execute(['nombre' => $nombre, 'correo' => $correo, 'hash' => $hash]);
        return (int) $pdo->lastInsertId();
    }

    public static function actualizarPerfil(int $id, string $nombre, ?string $telefono): void
    {
        $consulta = Conexion::abrir()->prepare(
            'UPDATE usuarios SET nombre = :nombre, telefono = :telefono WHERE id = :id'
        );
        $consulta->execute(['nombre' => $nombre, 'telefono' => $telefono, 'id' => $id]);
    }

    public static function actualizarCredenciales(int $id, string $correo, string $hash): void
    {
        $consulta = Conexion::abrir()->prepare(
            'UPDATE usuarios SET email = :correo, password = :hash WHERE id = :id'
        );
        $consulta->execute(['correo' => $correo, 'hash' => $hash, 'id' => $id]);
    }
}
