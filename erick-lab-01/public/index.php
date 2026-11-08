<?php
declare(strict_types=1);

// Única entrada HTTP. Elegimos el controlador a partir de ruta y método.
require_once dirname(__DIR__) . '/app/Controllers/InicioController.php';
require_once dirname(__DIR__) . '/app/Controllers/UsuarioController.php';
require_once dirname(__DIR__) . '/app/Controllers/SolicitudController.php';
require_once dirname(__DIR__) . '/app/Controllers/EquipoController.php';
require_once dirname(__DIR__) . '/app/Controllers/CatalogoController.php';

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

} elseif ($ruta === '/admin' && $metodo === 'GET') {
    (new EquipoController())->administrar();

} elseif ($ruta === '/admin/asignar' && $metodo === 'POST') {
    (new EquipoController())->asignar();

} elseif ($ruta === '/admin/calendario' && $metodo === 'GET') {
    (new EquipoController())->calendario();

} elseif ($ruta === '/admin/servicios' && $metodo === 'GET') {
    (new CatalogoController())->servicios();

} elseif ($ruta === '/admin/servicios/crear' && $metodo === 'POST') {
    (new CatalogoController())->crearServicio();

} elseif ($ruta === '/admin/servicios/actualizar' && $metodo === 'POST') {
    (new CatalogoController())->actualizarServicio();

} elseif ($ruta === '/admin/tecnicos' && $metodo === 'GET') {
    (new CatalogoController())->tecnicos();

} elseif ($ruta === '/admin/tecnicos/crear' && $metodo === 'POST') {
    (new CatalogoController())->crearTecnico();

} elseif ($ruta === '/admin/tecnicos/actualizar' && $metodo === 'POST') {
    (new CatalogoController())->actualizarTecnico();

} elseif ($ruta === '/tecnico' && $metodo === 'GET') {
    (new EquipoController())->misIncidenciasTecnico();

} elseif ($ruta === '/tecnico/aviso' && $metodo === 'GET') {
    (new EquipoController())->detalleTecnico();

} elseif ($ruta === '/tecnico/aviso/estado' && $metodo === 'POST') {
    (new EquipoController())->avanzarEstado();

} elseif (in_array($ruta, ['/registro', '/login', '/perfil', '/perfil/seguridad', '/salir', '/solicitar', '/mis-avisos', '/aviso', '/aviso/editar', '/aviso/cancelar', '/admin', '/admin/asignar', '/admin/calendario', '/admin/servicios', '/admin/servicios/crear', '/admin/servicios/actualizar', '/admin/tecnicos', '/admin/tecnicos/crear', '/admin/tecnicos/actualizar', '/tecnico', '/tecnico/aviso', '/tecnico/aviso/estado'], true)) {
    http_response_code(405);
    header('Allow: ' . (in_array($ruta, ['/salir', '/perfil/seguridad', '/aviso/cancelar', '/admin/asignar', '/admin/servicios/crear', '/admin/servicios/actualizar', '/admin/tecnicos/crear', '/admin/tecnicos/actualizar', '/tecnico/aviso/estado'], true) ? 'POST' : (in_array($ruta, ['/mis-avisos', '/aviso', '/admin', '/admin/calendario', '/admin/servicios', '/admin/tecnicos', '/tecnico', '/tecnico/aviso'], true) ? 'GET' : 'GET, POST')));
    echo 'Método no permitido';

} else {
    http_response_code(404);
    echo '404 - Página no encontrada';
}
