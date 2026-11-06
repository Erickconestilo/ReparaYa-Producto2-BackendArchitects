
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | ReparaYa</title>
</head>
<body>
    <h1>Crear cuenta en ReparaYa</h1>

    <form action="/registro" method="post">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" maxlength="100" autocomplete="name" required>

        <label for="correo">Correo</label>
        <input id="correo" name="correo" type="email" maxlength="100" autocomplete="email" required>

        <label for="clave">Contraseña</label>
        <input id="clave" name="clave" type="password" minlength="8" autocomplete="new-password" required>

        <button type="submit">Crear cuenta</button>
    </form>
</body>
</html>
