<?php
declare(strict_types=1);

// Solo CLI. La prueba no devuelve contraseñas ni hashes.
require dirname(__DIR__) . '/app/Models/Usuario.php';

$correo = $argv[1] ?? '';
if (!str_ends_with($correo, '@example.test')) {
    fwrite(STDERR, "Solo se permiten cuentas de prueba @example.test.\n");
    exit(2);
}

$usuario = Usuario::buscarPorCorreo($correo);
$valido = $usuario !== null
    && $usuario['rol'] === 'particular'
    && $usuario['password'] !== 'Demo-Cambiada-98765'
    && password_verify('Demo-Cambiada-98765', $usuario['password'])
    && !password_verify('Demo-SoloPrueba-12345', $usuario['password']);

echo 'REGISTRO_CIFRADO_Y_ROL=' . ($valido ? 'PASS' : 'FAIL') . PHP_EOL;
exit($valido ? 0 : 1);
