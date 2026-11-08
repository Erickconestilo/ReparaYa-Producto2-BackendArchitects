<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión | ReparaYa</title>
    <link rel="stylesheet" href="/assets/reparaya.css">
</head>
<body class="interior">
    <?php require __DIR__ . '/_cabecera.php'; ?>
    <h1>Entrar en ReparaYa</h1>
    <?php if ($registroCorrecto): ?>
        <p>Cuenta de prueba creada. Ahora inicia sesión.</p>
    <?php endif; ?>
    <form action="/login" method="post">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="correo">Correo</label>
        <input id="correo" name="correo" type="email" autocomplete="username" required>
        <label for="clave">Contraseña</label>
        <input id="clave" name="clave" type="password" autocomplete="current-password" required>
        <button type="submit">Entrar</button>
    </form>
    <p><a href="/registro">Crear cuenta</a></p>
</body>
</html>
