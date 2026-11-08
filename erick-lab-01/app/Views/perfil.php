<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil | ReparaYa</title>
</head>
<body>
    <h1>Mi perfil</h1>
    <?php if ($usuario['rol'] === 'particular'): ?>
        <p><a href="/solicitar">Solicitar reparación</a> · <a href="/mis-avisos">Mis avisos</a></p>
    <?php endif; ?>
    <?php if ($usuario['rol'] === 'admin'): ?>
        <p><a href="/admin">Administrar reparaciones</a></p>
    <?php endif; ?>
    <?php if ($usuario['rol'] === 'tecnico'): ?>
        <p><a href="/tecnico">Mis reparaciones asignadas</a></p>
    <?php endif; ?>
    <?php if (($_GET['guardado'] ?? '') === 'si'): ?><p>Datos guardados correctamente.</p><?php endif; ?>
    <?php if (($_GET['seguridad'] ?? '') === 'si'): ?><p>Correo y acceso actualizados.</p><?php endif; ?>
    <p>Correo: <?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?></p>
    <p>Rol: <?= htmlspecialchars($usuario['rol'], ENT_QUOTES, 'UTF-8') ?></p>
    <form action="/perfil" method="post">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" maxlength="100" required value="<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>">
        <label for="telefono">Teléfono (opcional)</label>
        <input id="telefono" name="telefono" maxlength="20" value="<?= htmlspecialchars((string) ($usuario['telefono'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">Guardar perfil</button>
    </form>
    <h2>Actualizar correo o contraseña</h2>
    <p>Para proteger tu cuenta, confirma siempre la contraseña actual.</p>
    <form action="/perfil/seguridad" method="post">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="correo_nuevo">Correo</label>
        <input id="correo_nuevo" name="correo" type="email" maxlength="100" required value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>">
        <label for="clave_actual">Contraseña actual</label>
        <input id="clave_actual" name="clave_actual" type="password" autocomplete="current-password" required>
        <label for="clave_nueva">Nueva contraseña (dejar vacía para conservarla)</label>
        <input id="clave_nueva" name="clave_nueva" type="password" autocomplete="new-password">
        <button type="submit">Actualizar acceso</button>
    </form>
    <form action="/salir" method="post">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>
