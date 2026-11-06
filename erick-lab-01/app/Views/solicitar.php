<!doctype html>
<html lang="es">
<head><meta charset="UTF-8"><title>Nueva reparación | ReparaYa</title></head>
<body>
    <h1>Solicitar una reparación</h1>
    <p><a href="/mis-avisos">Mis avisos</a> · <a href="/perfil">Mi perfil</a></p>
    <p>Una solicitud <strong>estándar</strong> requiere 48 horas como mínimo. Una <strong>urgente</strong> puede hacerse antes.</p>
    <form action="/solicitar" method="post">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="servicio">Servicio</label>
        <select id="servicio" name="servicio" required>
            <?php foreach ($servicios as $servicio): ?>
                <option value="<?= (int) $servicio['id'] ?>"><?= htmlspecialchars($servicio['nombre_especialidad'], ENT_QUOTES, 'UTF-8') ?> — base <?= number_format((float) $servicio['precio_base'], 2, ',', '.') ?> €</option>
            <?php endforeach; ?>
        </select>
        <label for="descripcion">Avería</label>
        <textarea id="descripcion" name="descripcion" minlength="10" maxlength="2000" required></textarea>
        <label for="direccion">Dirección</label>
        <input id="direccion" name="direccion" maxlength="255" required>
        <label for="telefono">Teléfono de contacto</label>
        <input id="telefono" name="telefono" maxlength="20" required>
        <label for="fecha">Fecha de servicio</label>
        <input id="fecha" name="fecha" type="date" required>
        <label for="franja">Franja</label>
        <select id="franja" name="franja"><option value="manana">Mañana (09:00)</option><option value="tarde">Tarde (16:00)</option></select>
        <label for="urgencia">Modalidad</label>
        <select id="urgencia" name="urgencia"><option value="Estándar">Estándar</option><option value="Urgente">Urgente</option></select>
        <button type="submit">Enviar solicitud</button>
    </form>
</body>
</html>
