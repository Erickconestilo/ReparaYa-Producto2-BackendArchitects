# UocAlert + UocStudy — Últimas prioridades ReparaYa P2

**Fuente académica verificada:** Canvas UOC curso 91603, entrega 989195; en consulta autenticada del 06/11/2026 estaba «En curso», sin presentar. **Límite:** 08/11/2026, 23:59, zona `Europe/Madrid`. **Nota:** UocAlert no creó alertas de calendario ni automatizaciones nuevas.

## Situación real para tomar decisiones

- **Disponible y probado localmente:** SQL oficial sin modificar, MySQL/PHP aislados, conexión PDO, usuarios con registro/login/perfil y **22/22 pruebas**, primer recorrido de solicitudes con **20/20 pruebas** y una edición limitada de avisos propios (`Pendiente`, más de 48 h, solo descripción/dirección/teléfono) con **26/26 pruebas adicionales**. **68/68 PASS** en Windows PowerShell 5.1, con consulta SQL real y regresiones; no supone los cuatro CRUD completos.
- **Repositorio privado creado y probado (06/11):** `Erickconestilo/ReparaYa-Producto2-BackendArchitects`, rama `main`, commit inicial `74f2739`, Docker reproducible desde una base nueva; aún falta invitar a Paula y verificar su acceso y aportaciones.
- **Sin verificar:** trabajo actual de Paula, calendario de servicios/técnicos, roles admin/técnico con transiciones, AWS, Word, mapa manuscrito, vídeo funcional y defensas. El trabajo local no constituye todavía una aplicación grupal entregable.
- **Riesgo:** gran cantidad de requisitos obligatorios para menos de dos días; no es realista afirmar que el Producto 2 puede terminarse con la sola evidencia actual. Pedir aclaración o posible alternativa al consultor si faltan aportaciones esenciales, sin asumir una prórroga concedida.

## Orden recomendado, por dependencia

1. **Ahora — bloqueo de grupo (bloque de 25 minutos):** pedir el **usuario exacto de GitHub de Paula** para invitarla con permiso de escritura al nuevo repositorio privado. Preguntar si ya dispone de otro repo/rama, archivos de su CRUD3/CRUD4, base SQL y qué funciona realmente. Mostrarle `migraciones/20261106_01_ampliacion_p2.sql` y la corrección `03` para conciliar sin pisar trabajo. No atribuirle archivos si no los comparte.
2. **Siguiente — integración técnica (bloques de 50–90 minutos):** unificar en Git, revisar los roles `admin`, `tecnico`, `particular`, asegurar el recorrido `Pendiente → Asignada → En curso → Finalizada`, rechazo de transición ilegal, calendario y listado de servicios con precio base. Probar una base NUEVA de integración si difiere del entorno de Erick, conservando todos los datos existentes.
3. **Antes del cierre — entrega (bloques de 50–90 minutos):** recopilar capturas sin secretos, registrar ≥4 interacciones IA con modificación/comprobación real, dibujar a mano mapa MVC/estados con roles, Word técnico, historial `git log --graph --oneline`, vídeo grupal + defensa de cada integrante de ≤2 minutos, validación AWS, ZIP sin `.env` y envío por Canvas. Verificar visualmente el estado **Entregado**; un ZIP en disco no lo demuestra.

## Preguntas que debe poder responder Erick

- ¿Qué ruta recibe `POST /registro` y qué función guarda el usuario?
- ¿Dónde se prepara el SQL y dónde se hace `password_hash`?
- ¿En qué momento una solicitud estándar es rechazable por 48 horas?
- ¿Por qué la cancelación necesita una transacción y un historial?
- ¿Qué fallo real hubo con `Estándar` y cómo comprobaste su corrección?
- ¿Qué parte hizo la IA y qué revisaste o modificaste tú personalmente?

## Riesgos de entrega y seguridad

- No usar Laravel (P2 exige PHP nativo MVC).
- No incluir `entorno-p2/.env` en el ZIP ni en Git; usar `.env.example`.
- No ejecutar `docker compose down -v` ni borrar volúmenes P1/P2.
- No presentar pruebas ficticias como datos de usuarios/clientes reales.
- No mover Trello a `Hecho` ni publicar Sites sin pruebas, conciliación con Paula y autorización.
- Capturas nuevas de los dos módulos **pendientes**; nueve originales de octubre conservados.
