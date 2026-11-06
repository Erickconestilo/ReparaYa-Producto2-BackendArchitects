-- Ampliación COMPATIBLE del SQL original (no modificar bbddReparaYa(1).sql).
-- Debe aplicarse UNA sola vez después de importar las cuatro tablas del profesor.
-- Propuesta de Erick, pendiente de conciliación con las tablas del equipo de Paula.

ALTER TABLE especialidades
    ADD COLUMN precio_base DECIMAL(10,2) NOT NULL DEFAULT 0.00;

ALTER TABLE incidencias
    MODIFY COLUMN localizador VARCHAR(20) NOT NULL,
    ADD COLUMN franja_horaria VARCHAR(40) NOT NULL DEFAULT '',
    ADD COLUMN telefono_contacto VARCHAR(20) NOT NULL DEFAULT '',
    MODIFY COLUMN estado ENUM('Pendiente', 'Asignada', 'En curso', 'Finalizada', 'Cancelada') DEFAULT 'Pendiente';

CREATE TABLE historial_estados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incidencia_id INT NOT NULL,
    estado_anterior VARCHAR(20) NULL,
    estado_nuevo VARCHAR(20) NOT NULL,
    actor_id INT NOT NULL,
    fecha_cambio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observacion VARCHAR(255) NULL,
    FOREIGN KEY (incidencia_id) REFERENCES incidencias(id),
    FOREIGN KEY (actor_id) REFERENCES usuarios(id),
    INDEX ix_historial_incidencia_fecha (incidencia_id, fecha_cambio, id)
);
