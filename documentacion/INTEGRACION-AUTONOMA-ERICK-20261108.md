# ReparaYa P2 — Integración autónoma de Erick y coordinación pendiente

Fecha de referencia: 8 de noviembre de 2026.

## Por qué se avanzó por separado

El Grupo 5 estaba formado por Erick Chacón González y Paula Gómez del Águila,
dos estudiantes que también trabajan y tienen horarios diferentes. La entrega
grupal de Producto 2 fue registrada por Paula el 7 de noviembre.

**Según lo comunicado por Erick**, hubo dificultades para mantener una
comunicación fluida y no había recibido respuesta de Paula cuando decidió
continuar el desarrollo de manera autónoma. Su intención fue aprovechar
ambos trabajos, recuperar una versión funcional y seguir aprendiendo, sin
alterar los materiales que ya habían sido entregados.

Esto **no significa** que Paula no trabajara ni supone una valoración negativa
de su participación. Su entrega y los conceptos de panel administrativo,
calendario y técnicos fueron un punto de partida para esta fase.

**Situación del equipo aún sin confirmar:** Erick no sabe si Paula seguirá
colaborando después de esta entrega. No debe documentarse su silencio como
una decisión de abandonar el proyecto.

## Qué hizo Erick después de la entrega

1. Comparó el material de su aplicación (usuarios y solicitudes) con el ZIP
   de Paula, detectando diferencias de estructura y esquema SQL.
2. Preparó el worktree y la rama aislada
   `feat/integracion-paula-local`, sin modificar el `main` ni el ZIP original.
3. Con ayuda de IA, **adaptó e implementó** en su base PHP MVC funcionalidades
   administrativas, asignaciones, perfil de técnico, transiciones de estados
   y calendario mensual/semanal/diario, tomando como referencia el ámbito
   funcional de Paula.
4. Amplió el catálogo de servicios y técnicos con altas, modificaciones,
   disponibilidad y desactivación lógica, manteniendo trazabilidad.
5. Organizó las pruebas y verificó **155/155 casos automatizados**.
   Comprobó manualmente en Docker/navegador un flujo completo con datos
   inventados, desde la asignación por un administrador hasta la finalización
   por el técnico y el registro del historial.

Los commits de esta rama muestran a Erick como responsable de **esta labor
posterior de integración y validación**. No se pretende adjudicarle la autoría
exclusiva de las ideas o del trabajo original de Paula.

## Criterio de autoría a partir de ahora

- El código, las mejoras, la documentación y las pruebas que Erick realice
  de forma independiente serán **aportaciones posteriores de Erick**, aunque
  partan de una idea o de una interfaz previa de Paula. Se indicará cuando
  exista reutilización o adaptación concreta de su trabajo.
- La colaboración de IA se declarará en los términos aplicables a la actividad.
  El autor humano del commit y el uso de herramientas son conceptos distintos.
- No se incluirá a Paula como autora o coautora de cambios nuevos por el solo
  hecho de pertenecer al grupo; tampoco se borrará su crédito por sus aportes.
- Si Paula responde y vuelve a colaborar, se distinguirán los nuevos cambios
  de cada integrante y los que realmente desarrollen juntos. Erick puede seguir
  trabajando en su rama mientras esa coordinación se resuelve.

## Qué está pendiente

- Enseñar a Paula el resultado cuando sea posible hablar con ella y recoger
  sus observaciones, sin presumir que continuará ni que ha abandonado el grupo.
- Revisar qué partes exactas del código original de Paula conviene incorporar,
  rehacer o mantener separadas; los archivos no se fusionaron de forma literal.
- Mejorar la interfaz, revisar las limitaciones técnicas y acordar cómo se
  describiría el proyecto conjunto si se publicase como versión final.
- Cualquier `push`, pull request, fusión a `main`, despliegue o nueva entrega
  académica queda **pendiente de una decisión posterior de Erick**. Esta nota
  no constituye autorización automática para realizar esos pasos.

## Mensaje sugerido para la propuesta de integración

**Título:** Erick integra y valida localmente los módulos de ReparaYa P2

**Resumen:** Tras la entrega grupal y sin haber conseguido coordinar una
revisión con Paula en ese momento, Erick continuó por iniciativa propia
la integración de usuarios/solicitudes con administración, calendario y
técnicos. Se conservaron las entregas originales, se reconocen los aportes
de ambos compañeros y se verificó el flujo con 155 pruebas automatizadas.
La rama se ofrece para revisión, sin solicitar aún la fusión a `main`.
