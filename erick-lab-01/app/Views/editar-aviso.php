<!doctype html>
<html lang="es">
<head><meta charset="UTF-8"><title>Editar solicitud | ReparaYa</title></head>
<body>
    <h1>Corregir solicitud <?= htmlspecialchars($aviso['localizador'], ENT_QUOTES, 'UTF-8') ?></h1>
    <p><a href="/aviso?id=<?= (int) $aviso['id'] ?>">Volver al detalle</a></p>
    <p>Solo puedes corregir estos datos si la solicitud continúa <strong>Pendiente</strong> y
       faltan más de 48 horas. El servicio, la fecha y la urgencia no se modifican desde aquí.</p>
    <form action="/aviso/editar" method="post">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id" value="<?= (int) $aviso['id'] ?>">
        <p><label for="descripcion">Descripción de la avería</label></p>
        <textarea id="descripcion" name="descripcion" minlength="10" maxlength="2000" required><?= htmlspecialchars($aviso['descripcion'], ENT_QUOTES, 'UTF-8') ?></textarea>
        <p><label for="direccion">Dirección</label></p>
        <input id="direccion" name="direccion" maxlength="255" required
               value="<?= htmlspecialchars($aviso['direccion'], ENT_QUOTES, 'UTF-8') ?>">
        <p><label for="telefono">Teléfono de contacto</label></p>
        <input id="telefono" name="telefono" maxlength="20" required
               value="<?= htmlspecialchars($aviso['telefono_contacto'], ENT_QUOTES, 'UTF-8') ?>">
        <p><button type="submit">Guardar cambios</button></p>
    </form>
</body>
</html>
