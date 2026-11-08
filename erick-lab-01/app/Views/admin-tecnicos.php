<?php $esc = static fn (mixed $valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); ?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Técnicos | ReparaYa</title></head>
<body>
    <h1>Gestión de técnicos</h1>
    <p><a href="/admin">Volver al panel</a> · <a href="/admin/servicios">Servicios y precios</a></p>
    <?php if (($_GET['creado'] ?? '') === 'si'): ?><p role="status">Técnico creado correctamente.</p><?php endif; ?>
    <?php if (($_GET['actualizado'] ?? '') === 'si'): ?><p role="status">Técnico actualizado.</p><?php endif; ?>
    <p>Un técnico con reparaciones activas no se puede desactivar ni cambiar de especialidad. No eliminamos cuentas para conservar la trazabilidad.</p>
    <h2>Dar de alta un técnico</h2>
    <form method="post" action="/admin/tecnicos/crear">
        <input type="hidden" name="_csrf" value="<?= $esc($csrfToken) ?>">
        <label>Nombre <input name="nombre" maxlength="100" required></label>
        <label>Correo <input name="correo" type="email" maxlength="100" required></label>
        <label>Contraseña inicial <input name="clave" type="password" minlength="8" maxlength="72" autocomplete="new-password" required></label>
        <label>Especialidad <select name="especialidad_id" required>
            <option value="">Selecciona</option>
            <?php foreach ($especialidades as $especialidad): ?>
                <?php if ((int) $especialidad['activa'] === 1): ?>
                    <option value="<?= (int) $especialidad['id'] ?>"><?= $esc($especialidad['nombre_especialidad']) ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select></label>
        <button type="submit">Crear técnico</button>
    </form>

    <h2>Técnicos registrados</h2>
    <ul>
    <?php foreach ($tecnicos as $tecnico): ?>
        <li>
            <p><?= $esc($tecnico['email']) ?></p>
            <form method="post" action="/admin/tecnicos/actualizar">
                <input type="hidden" name="_csrf" value="<?= $esc($csrfToken) ?>">
                <input type="hidden" name="id" value="<?= (int) $tecnico['id'] ?>">
                <label>Nombre <input name="nombre" maxlength="100" required value="<?= $esc($tecnico['nombre_completo']) ?>"></label>
                <label>Especialidad <select name="especialidad_id" required>
                    <?php foreach ($especialidades as $especialidad): ?>
                        <?php $actual = (int) $tecnico['especialidad_id'] === (int) $especialidad['id']; ?>
                        <option value="<?= (int) $especialidad['id'] ?>"
                            <?= $actual ? 'selected' : '' ?>
                            <?= (int) $especialidad['activa'] !== 1 ? 'disabled' : '' ?>>
                            <?= $esc($especialidad['nombre_especialidad']) ?><?= (int) $especialidad['activa'] !== 1 ? ' (inactiva)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select></label>
                <label>Disponible <input name="disponible" type="checkbox" value="1" <?= (int) $tecnico['disponible'] === 1 ? 'checked' : '' ?>></label>
                <button type="submit">Guardar técnico</button>
            </form>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php if (!$tecnicos): ?><p>No hay técnicos registrados.</p><?php endif; ?>
</body>
</html>
