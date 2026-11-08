<?php $esc = static fn (mixed $valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); ?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Servicios | ReparaYa</title></head>
<body>
    <h1>Servicios y especialidades</h1>
    <p><a href="/admin">Volver al panel</a> · <a href="/admin/tecnicos">Técnicos</a></p>
    <?php if (($_GET['creado'] ?? '') === 'si'): ?><p role="status">Servicio creado correctamente.</p><?php endif; ?>
    <?php if (($_GET['actualizado'] ?? '') === 'si'): ?><p role="status">Servicio actualizado.</p><?php endif; ?>
    <p>Desactivar un servicio impide nuevas solicitudes, pero conserva las reparaciones anteriores y su historial.</p>
    <h2>Crear un servicio</h2>
    <form method="post" action="/admin/servicios/crear">
        <input type="hidden" name="_csrf" value="<?= $esc($csrfToken) ?>">
        <label>Nombre <input name="nombre" required maxlength="50"></label>
        <label>Precio base (€) <input name="precio" type="number" min="0" max="99999999.99" step="0.01" required></label>
        <button type="submit">Crear servicio</button>
    </form>
    <h2>Catálogo existente</h2>
    <ul>
    <?php foreach ($servicios as $servicio): ?>
        <li>
            <form method="post" action="/admin/servicios/actualizar">
                <input type="hidden" name="_csrf" value="<?= $esc($csrfToken) ?>">
                <input type="hidden" name="id" value="<?= (int) $servicio['id'] ?>">
                <label>Nombre <input name="nombre" maxlength="50" required value="<?= $esc($servicio['nombre_especialidad']) ?>"></label>
                <label>Precio (€) <input name="precio" type="number" min="0" max="99999999.99" step="0.01" required value="<?= $esc($servicio['precio_base']) ?>"></label>
                <label>Activo <input name="activa" type="checkbox" value="1" <?= (int) $servicio['activa'] === 1 ? 'checked' : '' ?>></label>
                <button type="submit">Guardar cambios</button>
            </form>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php if (!$servicios): ?><p>No hay servicios.</p><?php endif; ?>
</body>
</html>
