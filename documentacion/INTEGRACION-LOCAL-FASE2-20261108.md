# ReparaYa P2 — Fase 2 local: maestros de técnicos y servicios

Fecha: 08/11/2026. Grupo 5, Erick Chacón González y Paula Gómez del Águila.

## Atribución y alcance

Se continúa el trabajo académico ya entregado sin modificar los ZIP de origen. El alcance de técnicos/servicios y el concepto de administración proceden del reparto de equipo y del ZIP de Paula; esta implementación se adapta al router, modelo PDO, controles y base de datos de la copia local de integración. **No afirmar que toda la aplicación se fusionó ni atribuir exclusivamente estas ideas a un solo participante.**

## Funcionalidades nuevas

- `/admin/servicios`: alta, edición de nombre/precio, desactivación y reactivación de especialidades. Baja **lógica**, sin `DELETE` sobre tablas referenciadas.
- `/admin/tecnicos`: creación de cuenta de técnico (contraseña con `password_hash`), edición de nombre/especialidad y disponibilidad. Baja **lógica** con la columna `tecnicos.disponible` existente.
- Bloqueo de desactivación/cambio de especialidad del técnico si tiene incidencias `Asignada` o `En curso`; se evitan reparaciones abandonadas.
- Servicio desactivado: desaparece del formulario de nuevas solicitudes y `Solicitud::existeServicio` lo rechaza, sin ocultar incidencias históricas.
- Las operaciones que cambian usuario + perfil se ejecutan en transacciones; POSTs protegidos por sesión y token CSRF, comprobando rol `admin` desde MySQL.
- Precios ficticios de prueba, no tarifas comerciales reales. Validación de importes positivos o cero, hasta dos decimales.
- No se implementa el borrado físico de usuarios, técnicos o especialidades: las relaciones de las incidencias requieren conservar trazabilidad.

## Migración y convivencia

- **Nueva migración de una sola ejecución** `migraciones/20261108_05_catalogo_activo.sql`: añade `especialidades.activa BOOLEAN DEFAULT TRUE`.
- `entorno-p2/compose.yaml` del **worktree** importa 01 (original), 02 (ampliación Erick), 03 (DEMO), 04 (normalización) y 05 (activo) cuando inicializa una base nueva. **No** importar la ampliación SQL de Paula sobre esta misma base, porque crea campos y tablas duplicados.
- Para una BD ya existente de este worktree, los scripts de `/docker-entrypoint-initdb.d` no se vuelven a ejecutar: hace falta aplicar la nueva migración **una sola vez y con copia de seguridad**. Este paso no se ha hecho sobre ninguna BD persistente.
- Para evitar afectar al P2 original, el Compose de integración utiliza nombre `reparaya-p2-integracion-local`, su propio volumen, imagen `reparaya-p2-web-integracion:local`, puerto local `127.0.0.1:8084` y `.env` distinto generado dentro del worktree. Los tres scripts antiguos de pruebas HTTP se han ajustado únicamente en esta copia para que apunten a 8084.
- La prueba automatizada aislada **no usa Compose, no publica puertos, no crea volumen persistente y elimina su contenedor temporal**.

## Pruebas realmente ejecutadas

Comando (solo en worktree):

```powershell
.\entorno-p2\probar-integracion-aislada.ps1
```

- **20/20** modelo original de admin, técnico, incidencias y calendario.
- **25/25** casos nuevos MySQL de catálogo y técnicos: precios, duplicados, altas, edición, cambios de estado, bajas lógicas e historial.
- **42/42** pruebas HTTP, incluidas 22 nuevas de formularios, permisos, CSRF, validación de campos, redirecciones y efectos reales de las altas/bajas.
- **87/87 en conjunto** sobre la BD y el servidor HTTP temporales.
- Lint PHP y `docker compose config --quiet` correctos.
- No se han repetido las 68 pruebas anteriores desde los scripts PowerShell sobre un Compose persistente. Tampoco se ha validado AWS ni una ejecución visual con un navegador real.

## Riesgos y próximos pasos

1. El `README.md` de la raíz viene del estado inicial del proyecto y contiene información histórica desactualizada (repositorio privado, entrega pendiente). Este documento y el de fase 1 describen mejor el estado de la **copia local**. Corregir README antes de una publicación acordada.
2. Comprobar visualmente las pantallas, la accesibilidad y la experiencia móvil con datos exclusivamente DEMO.
3. Endurecer duplicados simultáneos, concurrencia en cambios de disponibilidad, validaciones de entrada y protección ante abuso antes de cualquier despliegue.
4. Revisar con Paula el repositorio y las versiones que ella realmente creó; el `.git` de su ZIP no tiene commits recuperables.
5. Acordar autoría, ramas y pruebas de aceptación antes de hacer un merge o push.

**Estado:** fase 2 local probada; no subida a GitHub, no incorporada a `main`, no entregada en Canvas y no publicada en Google Sites.
