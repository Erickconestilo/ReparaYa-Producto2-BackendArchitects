<?php $esc = static fn (mixed $valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); ?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Mis reparaciones | ReparaYa</title></head>
<body>
    <h1>Mis reparaciones asignadas</h1>
    <p><a href="/perfil">Mi perfil</a></p>
    <?php if (!$incidencias): ?><p>No tienes reparaciones asignadas.</p><?php endif; ?>
    <ul>
        <?php foreach ($incidencias as $incidencia): ?>
            <li>
                <a href="/tecnico/aviso?id=<?= (int) $incidencia['id'] ?>"><?= $esc($incidencia['localizador']) ?></a>
                — <?= $esc($incidencia['nombre_especialidad']) ?>
                — <?= $esc($incidencia['estado']) ?>
                — <?= $esc($incidencia['fecha_servicio']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
