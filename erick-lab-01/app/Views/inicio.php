<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
    <main>
        <h1><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
        <p>Ruta <code>/</code> → <code>InicioController</code> → <code>inicio.php</code>.</p>
    </main>
</body>
</html>
