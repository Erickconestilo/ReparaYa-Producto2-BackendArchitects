-- Corrección por un problema REAL de codificación en la importación automática de MySQL.
-- El archivo inicial del profesor está correcto y se conserva intacto.
-- MySQL había guardado 'EstÃ¡ndar' en el ENUM; este ALTER normaliza el valor.
SET NAMES utf8mb4;
ALTER TABLE incidencias
    MODIFY COLUMN tipo_urgencia ENUM('Estándar', 'Urgente') DEFAULT 'Estándar';
