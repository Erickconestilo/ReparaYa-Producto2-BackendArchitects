# ReparaYa P2 — Integración local, fase 1 — 08/11/2026

## Contexto y atribución

- Proyecto académico compartido del Grupo 5: Erick Chacón González y Paula Gómez del Águila.
- Insumos leídos: ZIP individual de Erick y ZIP grupal presentado por Paula, además de la auditoría `AUDITORIA-INTEGRACION-PAULA-ERICK-20261108.md` guardada **en el directorio original**, fuera de este worktree.
- La idea funcional de administración, asignación de técnicos, panel de técnico y calendario con vistas mes/semana/día se tomó del código de Paula. Las implementaciones que hay aquí son **adaptaciones nuevas** al router, tablas, seguridad y estilo de la aplicación de Erick; no se publican como si fueran obra exclusiva de uno de los dos.
- No se copiaron del ZIP de Paula configuraciones, `.env`, credenciales, paquetes WordPress/Laravel ni volcados de usuarios.
- Esta fase es una rama local de integración, **no sustituye ninguna entrega ni contiene una integración final acordada con Paula**.

## Aislamiento

- Rama local: `feat/integracion-paula-local`.
- Worktree independiente: `C:\Users\guill\Documents\UOC\FP448-Backend-PHP-MVC\Producto-2-integracion-local`.
- El proyecto previo se conserva en `Producto-2`, rama `main`, sin modificaciones de código por esta fase.
- Sin `push`, pull request, merge, despliegue AWS ni cambios en Canvas/Google Sites.

## Funciones implementadas

1. `GET /admin`: listado de incidencias para admin, con técnicos disponibles compatibles por especialidad.
2. `POST /admin/asignar`: verifica rol desde la BD, token CSRF, estado Pendiente, técnico disponible y especialidad coincidente; registra `Pendiente → Asignada` con el admin como actor dentro de transacción.
3. `GET /tecnico`, `GET /tecnico/aviso?id=...`: listado y detalle accesibles solo por el técnico asignado. La vista muestra el historial correcto desde `historial_estados`.
4. `POST /tecnico/aviso/estado`: valida CSRF, rol, titularidad y solo permite `Asignada → En curso → Finalizada`. El nuevo estado y el evento de historial se guardan juntos en una transacción.
5. `GET /admin/calendario?vista=mes|semana|dia&fecha=AAAA-MM-DD`: calendario administrativo con las tres vistas del concepto de Paula, datos limitados al período consultado, validación de fecha y salida HTML escapada.
6. Enlaces específicos en `/perfil` según rol. El router original de Erick permanece como única entrada HTTP.

## Comprobaciones ejecutadas

- Lint de los **27 archivos PHP** de aplicación, vistas y tests: sin errores de sintaxis.
- `entorno-p2/probar-integracion-aislada.ps1`: importa SQL oficial + migraciones de Erick en un MySQL 8 **efímero**, sin puertos publicados, con almacenamiento temporal.
- **20/20 pruebas directas contra MySQL**: especialidades, permisos, asignación, cambios de estado, orden y autoría del historial, calendario mes/semana/día.
- **20/20 pruebas HTTP**: login por roles, permisos 302/403, CSRF 403, asignación 303, transiciones 409/303, calendario 200/422 y comprobación final en SQL.
- La prueba HTTP inicia PHP en la misma red aislada del MySQL temporal, sin exponer un puerto al equipo.
- El script elimina el contenedor y su almacenamiento temporal en `finally`; **no** toca el volumen de MySQL normal ni la base de datos de P1/P2 en uso.

Para reproducir en Docker Desktop con la imagen `reparaya-p2-web:local` disponible:

```powershell
Set-Location C:\Users\guill\Documents\UOC\FP448-Backend-PHP-MVC\Producto-2-integracion-local
.\entorno-p2\probar-integracion-aislada.ps1
```

## Qué NO se ha implementado ni se debe dar por validado

- CRUD completo de técnicos (alta, modificación, baja/desactivación) y CRUD de especialidades/precios.
- Integración de todas las pantallas específicas de Paula; las vistas actuales son una adaptación básica, no una copia pixel a pixel.
- Gestión administrativa de cancelaciones, reasignaciones, notificaciones o agenda avanzada.
- Sesiones paralelas, gran volumen, accesibilidad detallada y seguridad de producción.
- AWS, WordPress/Laravel, despliegue, repositorio compartido de Paula, ni un acuerdo explícito sobre la versión final de ambos trabajos.
- No se han repetido en esta fase las 68 pruebas anteriores de Erick; hay 40 pruebas **nuevas**, independientes, descritas arriba.

## Siguiente fase sugerida

1. Crear CRUD seguro de técnicos y servicios sobre la migración única que ya usa este worktree; no aplicar el segundo SQL de Paula encima.
2. Ampliar pruebas de permisos y operaciones de admin, y decidir si conviene introducir cambios de agenda y edición de precios.
3. Hablar con Paula para confirmar qué versión considera definitiva y recuperar su historial Git original si existe.
4. Revisar juntos el resultado antes de proponer publicar o fusionar la integración.

**Estado:** fase 1 funcional y probada en entorno aislado; solo local, no publicada.
