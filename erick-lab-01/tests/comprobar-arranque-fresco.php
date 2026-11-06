<?php
declare(strict_types=1);

// Comprueba una base vacía recién inicializada por Docker Compose.
// No crea ni modifica datos: es útil al clonar el proyecto por primera vez.
require_once dirname(__DIR__) . '/app/Database/Conexion.php';

try {
    $pdo = Conexion::abrir();
    $tablas = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    $esperadas = ['especialidades', 'historial_estados', 'incidencias', 'tecnicos', 'usuarios'];
    sort($tablas);
    sort($esperadas);
    if ($tablas !== $esperadas) {
        throw new RuntimeException('Tablas distintas a las cinco esperadas: ' . implode(', ', $tablas));
    }

    $catalogo = $pdo->query('SELECT nombre_especialidad FROM especialidades ORDER BY nombre_especialidad')
        ->fetchAll(PDO::FETCH_COLUMN);
    if ($catalogo !== ['Electricidad (DEMO)', 'Fontanería (DEMO)']) {
        throw new RuntimeException('El catálogo de demostración no coincide con el esperado.');
    }

    $campos = $pdo->query("SHOW COLUMNS FROM incidencias LIKE 'tipo_urgencia'")->fetch();
    if (!$campos || !str_contains((string) $campos['Type'], 'Estándar')) {
        throw new RuntimeException('Falta la urgencia Estándar con acentuación correcta.');
    }

    echo "PASS: Cinco tablas del esquema original y la ampliación\n";
    echo "PASS: Catálogo DEMO importado con UTF-8\n";
    echo "PASS: ENUM Estándar correctamente acentuado\n";
    echo "BOOTSTRAP_FRESCO=PASS\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'BOOTSTRAP_FRESCO=FAIL: ' . $error->getMessage() . "\n");
    exit(1);
}
