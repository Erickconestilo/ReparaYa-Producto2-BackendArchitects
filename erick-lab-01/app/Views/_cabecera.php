<?php
// Navegación visual. La autorización real permanece en los controladores.
$rutaNavegacion = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$sesionNavegacion = (int) ($_SESSION['usuario_id'] ?? 0) > 0;
$rolNavegacion = $usuario['rol'] ?? null;
$esZonaAdmin = str_starts_with($rutaNavegacion, '/admin') || $rolNavegacion === 'admin';
$esZonaTecnico = str_starts_with($rutaNavegacion, '/tecnico') || $rolNavegacion === 'tecnico';
$esZonaCliente = in_array($rutaNavegacion, ['/solicitar', '/mis-avisos', '/aviso', '/aviso/editar'], true)
    || $rolNavegacion === 'particular';
?>
<header class="cabecera cabecera-interior">
    <a class="marca" href="/" aria-label="ReparaYa, inicio">Repara<span>Ya</span></a>
    <nav aria-label="Navegación principal">
        <a href="/">Inicio</a>
        <?php if ($sesionNavegacion): ?>
            <?php if ($esZonaAdmin): ?>
                <a href="/admin">Reparaciones</a>
                <a href="/admin/calendario">Calendario</a>
                <a href="/admin/servicios">Servicios</a>
                <a href="/admin/tecnicos">Técnicos</a>
            <?php elseif ($esZonaTecnico): ?>
                <a href="/tecnico">Mis tareas</a>
            <?php elseif ($esZonaCliente): ?>
                <a href="/solicitar">Nueva solicitud</a>
                <a href="/mis-avisos">Mis avisos</a>
            <?php endif; ?>
            <a class="enlace-destacado" href="/perfil">Mi perfil</a>
        <?php else: ?>
            <a href="/login">Iniciar sesión</a>
            <a class="enlace-destacado" href="/registro">Crear cuenta</a>
        <?php endif; ?>
    </nav>
</header>
