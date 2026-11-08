<?php
$esc = static fn (mixed $valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$sesionIniciada = (int) ($_SESSION['usuario_id'] ?? 0) > 0;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReparaYa | Gestión de reparaciones</title>
    <link rel="stylesheet" href="/assets/reparaya.css">
</head>
<body>
    <header class="cabecera">
        <a class="marca" href="/" aria-label="ReparaYa, inicio">Repara<span>Ya</span></a>
        <nav aria-label="Navegación principal">
            <?php if ($sesionIniciada): ?>
                <a href="/perfil">Mi perfil y mis opciones</a>
            <?php else: ?>
                <a href="/login">Iniciar sesión</a>
                <a class="enlace-destacado" href="/registro">Crear cuenta</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <section class="portada" aria-labelledby="titulo-principal">
            <p class="etiqueta">Proyecto ReparaYa · PHP MVC</p>
            <h1 id="titulo-principal">Una forma sencilla de gestionar tus reparaciones.</h1>
            <p class="introduccion">Solicita una reparación, consulta su estado y sigue el trabajo desde tu cuenta.</p>
            <div class="acciones">
                <?php if ($sesionIniciada): ?>
                    <a class="boton boton-primario" href="/perfil">Entrar a mi espacio</a>
                <?php else: ?>
                    <a class="boton boton-primario" href="/registro">Empezar ahora</a>
                    <a class="boton boton-secundario" href="/login">Ya tengo cuenta</a>
                <?php endif; ?>
            </div>
        </section>
        <section class="funciones" aria-label="Qué puedes hacer en ReparaYa">
            <article><span class="numero">01</span><h2>Clientes</h2><p>Crear solicitudes y consultar avisos e historial.</p></article>
            <article><span class="numero">02</span><h2>Administración</h2><p>Organizar citas, servicios y asignaciones.</p></article>
            <article><span class="numero">03</span><h2>Técnicos</h2><p>Ver tareas asignadas y actualizar su progreso.</p></article>
        </section>
        <details class="nota-laboratorio">
            <summary>Nota del laboratorio de aprendizaje</summary>
            <p><?= $esc($mensaje) ?></p>
            <p>Ruta <code>/</code> → <code>InicioController</code> → <code>inicio.php</code>.</p>
        </details>
    </main>
    <footer>ReparaYa · Proyecto académico de desarrollo web</footer>
</body>
</html>
