# ReparaYa Producto 2 — actualización integrada de Erick (08/11/2026)

Esta es una ampliación posterior a la entrega grupal, preparada por Erick Chacón
González con asistencia de IA y tomando como referencia el ámbito funcional de
administración, técnicos y calendario que aportó Paula Gómez del Águila.
Se conservan el trabajo anterior y la separación de autorías.

## Mejora final

- El administrador puede filtrar reparaciones por estado: todas, Pendiente,
  Asignada, En curso, Finalizada y Cancelada.
- Los valores del filtro se validan en el controlador; la consulta usa
  parámetros preparados de PDO. Los filtros desconocidos responden con HTTP 422.
- Se revisaron algunas tildes y mensajes del panel de administración.

## Evidencia comprobada

- Sin errores de sintaxis en los tres archivos PHP modificados.
- `git diff --check`: sin errores de formato.
- Batería aislada de integración: **92/92** pruebas aprobadas: 20 de gestión,
  25 de catálogo y 47 HTTP, incluidas cinco pruebas nuevas del filtro.
- La base temporal de pruebas se eliminó. El entorno de demostración local
  `127.0.0.1:8084` se mantiene activo para la revisión de Erick.
- En una revisión anterior constaban **68** pruebas aprobadas de usuarios
  y solicitudes; no se repitieron en esta revisión. Total histórico: **160**.

## Alcance y límites

El resultado permite recorrer las funciones principales de cliente,
administrador y técnico y deja una mejora verificable sobre el trabajo inicial.
No acredita que se haya desplegado en AWS ni una validación completa en móvil,
accesibilidad o seguridad. Tampoco implica que Paula haya confirmado una fusión
definitiva ni que la nueva entrega se haya aceptado formalmente en Canvas.

La rama autorizada para publicación es `feat/integracion-paula-local`.
No se debe fusionar a `main` ni enviar un comentario a Canvas hasta contar
con la revisión y autorización correspondiente de Erick.
