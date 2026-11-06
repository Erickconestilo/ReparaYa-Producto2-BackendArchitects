<?php
declare(strict_types=1);

// Única entrada HTTP. Elegimos el controlador a partir de ruta y método.
require_once dirname(__DIR__) . '/app/Controllers/InicioController.php';
require_once dirname(__DIR__) . '/app/Controllers/UsuarioController.php';
require_once dirname(__DIR__) . '/app/Controllers/SolicitudController.php';

header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
    'path' => '/',
]);
session_start();

$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);


$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($ruta === '/' && $metodo === 'GET') {
    (new InicioController())->mostrar();

} elseif ($ruta === '/registro' && $metodo === 'GET') {
    (new InicioController())->mostrarRegistro();

} elseif ($ruta === '/registro' && $metodo === 'POST') {
    (new UsuarioController())->procesarRegistro();

} elseif ($ruta === '/login' && $metodo === 'GET') {
    (new UsuarioController())->mostrarLogin();

} elseif ($ruta === '/login' && $metodo === 'POST') {
    (new UsuarioController())->procesarLogin();

} elseif ($ruta === '/perfil' && $metodo === 'GET') {
    (new UsuarioController())->mostrarPerfil();

} elseif ($ruta === '/perfil' && $metodo === 'POST') {
    (new UsuarioController())->actualizarPerfil();

} elseif ($ruta === '/perfil/seguridad' && $metodo === 'POST') {
    (new UsuarioController())->actualizarCredenciales();

} elseif ($ruta === '/salir' && $metodo === 'POST') {
    (new UsuarioController())->salir();

} elseif ($ruta === '/solicitar' && $metodo === 'GET') {
    (new SolicitudController())->formulario();

} elseif ($ruta === '/solicitar' && $metodo === 'POST') {
    (new SolicitudController())->crear();

} elseif ($ruta === '/mis-avisos' && $metodo === 'GET') {
    (new SolicitudController())->misAvisos();

} elseif ($ruta === '/aviso' && $metodo === 'GET') {
    (new SolicitudController())->detalle();

} elseif ($ruta === '/aviso/editar' && $metodo === 'GET') {
    (new SolicitudController())->editarFormulario();

} elseif ($ruta === '/aviso/editar' && $metodo === 'POST') {
    (new SolicitudController())->editar();

} elseif ($ruta === '/aviso/cancelar' && $metodo === 'POST') {
    (new SolicitudController())->cancelar();

} elseif (in_array($ruta, ['/registro', '/login', '/perfil', '/perfil/seguridad', '/salir', '/solicitar', '/mis-avisos', '/aviso', '/aviso/editar', '/aviso/cancelar'], true)) {
    http_response_code(405);
    header('Allow: ' . (in_array($ruta, ['/salir', '/perfil/seguridad', '/aviso/cancelar'], true) ? 'POST' : (in_array($ruta, ['/mis-avisos', '/aviso'], true) ? 'GET' : 'GET, POST')));
    echo 'Método no permitido';

} else {
    http_response_code(404);
    echo '404 - Página no encontrada';
}
