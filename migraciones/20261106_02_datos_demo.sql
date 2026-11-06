-- Únicamente datos inventados de desarrollo: NO son el catálogo ni los precios reales de Paula.
SET NAMES utf8mb4;
INSERT INTO especialidades (nombre_especialidad, precio_base)
SELECT 'Fontanería (DEMO)', 55.00
WHERE NOT EXISTS (SELECT 1 FROM especialidades WHERE nombre_especialidad = 'Fontanería (DEMO)');

INSERT INTO especialidades (nombre_especialidad, precio_base)
SELECT 'Electricidad (DEMO)', 65.00
WHERE NOT EXISTS (SELECT 1 FROM especialidades WHERE nombre_especialidad = 'Electricidad (DEMO)');
