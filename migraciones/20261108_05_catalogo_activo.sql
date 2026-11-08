-- Fase 2 local: desactivacion logica de servicios.
-- Una sola vez, despues de 20261106_01; nunca aplicar ambas ampliaciones de Erick y Paula.
ALTER TABLE especialidades
    ADD COLUMN activa BOOLEAN NOT NULL DEFAULT TRUE;

