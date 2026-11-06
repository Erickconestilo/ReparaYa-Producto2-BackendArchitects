<?php
declare(strict_types=1);

// El controlador prepara los datos. No contiene HTML ni SQL.
final class InicioController
{
    public function mostrarRegistro(): void
    {
        $csrfToken = UsuarioController::token();
        require dirname(__DIR__) . '/Views/registro.php';
    }
    public function mostrar(): void
    {
        $titulo = 'ReparaYa — Laboratorio MVC';
        $mensaje = 'probando el php y mejorando mi conocimiento de php para reparar ya';

        require dirname(__DIR__) . '/Views/inicio.php';
    }
}
