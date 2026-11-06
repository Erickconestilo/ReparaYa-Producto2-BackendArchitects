# ReparaYa P2 — Prueba del SQL inicial del profesor (06/11/2026)

## Procedencia e integridad

- Aula FP.448, curso 91603: `https://aula.uoc.edu/courses/91603/files/14003623?wrap=1`.
- Archivo: `SQL-oficial-original/bbddReparaYa(1).sql`.
- Tamaño: **2042 bytes**.
- SHA-256 original de Descargas y copia en P2 (coinciden): `C517DC46D14E6F3A6FA27C5DD2B7849C0DCA01350151E54D9C3BDA79E1AADB4B`.
- El SQL original no se modificó. Contiene cuatro sentencias `CREATE TABLE` y ninguna `INSERT`.

## Prueba ejecutada

- Motor: Docker Engine 29.7.2; imagen existente `mysql:8.0`.
- Contenedor desechable `reparaya-p2-sql-audit-20261106`, sin red ni puertos publicados, con `/var/lib/mysql` temporal en memoria y el directorio del SQL montado **solo lectura**.
- Base creada exclusivamente dentro de ese contenedor: `reparaya_p2_pruebas`.
- Importación del archivo original: **correcta, exit code 0**.
- `SHOW TABLES` devuelve: `especialidades`, `incidencias`, `tecnicos`, `usuarios`.
- Recuentos: `usuarios = 0`, `incidencias = 0` (el SQL no aporta datos iniciales).
- Las restricciones de clave externa presentes tras importar suman **5**.
- Se contrastó la definición de columnas contra `information_schema`: `localizador` es `varchar(12)`; `estado` no contempla `En curso`; `tipo_urgencia` incluye `Estándar` y `Urgente`. Los bytes UTF-8 de `Estándar` se verificaron por `HEX(COLUMN_TYPE)`.

Esta prueba **no demuestra todavía** que ReparaYa funcione con PDO, ni que los cuatro CRUD, sesiones, permisos, 48 horas, calendario o AWS estén terminados.

## Ampliaciones pendientes (en archivo aparte, sin reescribir el SQL entregado)

1. Incorporar el estado `En curso` y una tabla con historial de transiciones, actor y fecha.
2. Establecer precio base de servicio y los datos necesarios para franja horaria y teléfono de contacto de la incidencia.
3. Resolver la longitud de `localizador`: el ejemplo `REP-2026-XXXX` tiene 13 caracteres, pero el campo admite 12.
4. Acordar con Paula la estructura compartida, ramas Git y responsable de cada cambio.

## Siguiente prueba

Con el equipo: elegir una base **de desarrollo**, configurar PHP nativo con PDO y credenciales locales fuera de Git, verificar `SELECT 1`, luego consultar las cuatro tablas y crear un registro ficticio seguro. No importar sobre la base existente de P1 ni sobre datos de terceros sin verificar el destino. Probar cualquier ampliación primero sobre una copia aislada.

