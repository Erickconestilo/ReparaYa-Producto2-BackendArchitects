<?php
declare(strict_types=1);

// PDO es el puente entre PHP y MySQL; no crea conexiones usando datos del formulario.
final class Conexion
{
    public static function abrir(): PDO
    {
        $host = getenv('DB_HOST') ?: '';
        $puerto = getenv('DB_PORT') ?: '3306';
        $nombre = getenv('DB_NAME') ?: '';
        $usuario = getenv('DB_USER') ?: '';
        $clave = getenv('DB_PASSWORD');

        if ($host === '' || $nombre === '' || $usuario === '' || $clave === false) {
            throw new RuntimeException('Faltan variables de conexión a MySQL.');
        }

        return new PDO(
            "mysql:host={$host};port={$puerto};dbname={$nombre};charset=utf8mb4",
            $usuario,
            $clave,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
