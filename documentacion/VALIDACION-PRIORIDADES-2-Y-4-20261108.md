# ReparaYa P2 — Validación de prioridades 2 y 4

**Fecha:** 08/11/2026. **Responsable de esta revisión posterior:** Erick Chacón González, con asistencia de IA.

**Rama aislada:** `feat/integracion-paula-local` (`Producto-2-integracion-local`). No se modificó `main`, la entrega original del grupo ni el material aportado por Paula.

## Prioridad 2 — Revisión visual de la nueva interfaz

- Se abrió la aplicación renovada en una pestaña temporal del navegador real, en `http://127.0.0.1:8084/`, con resolución de escritorio aproximada de 1704 × 1090.
- La portada presenta una cabecera con la marca ReparaYa, navegación clara, zona introductoria y tarjetas informativas; los enlaces para crear cuenta e iniciar sesión son visibles.
- Se revisó la estructura y el funcionamiento de los formularios de registro, inicio de sesión, solicitud, perfil, catálogo de servicios y alta de técnicos, y los paneles de administración y técnico.
- Los controles, títulos, campos y mensajes de confirmación aparecieron visibles y operables. No se detectó un bloqueo visual del flujo de escritorio probado.
- **A pulir, no bloqueante:** los paneles muestran algunos enlaces de navegación duplicados debajo del encabezado y hay títulos sin tildes (`Administracion`, `Reparacion`, etc.). Una revisión de microtexto y consistencia visual mejoraría el acabado.
- **No comprobado:** apariencia real en móvil/tablet, tecnologías de asistencia y contraste WCAG; el resultado visual de escritorio no permite dar por superadas esas pruebas.

## Prioridad 4 — Recorrido real de extremo a extremo

El recorrido se hizo por los formularios de la interfaz, con cuentas `example.test`, una dirección ficticia, un servicio ficticio de cerrajería a 45 € y una base MySQL exclusiva del proyecto Compose `reparaya-p2-integracion-local`. La cuenta de administrador se preparó promoviendo **solo un usuario de esa base descartable**; las demás acciones se hicieron desde el navegador.

| Paso | Resultado observado |
| --- | --- |
| Registrar e iniciar sesión como cliente | Cuenta creada y perfil con rol `particular`. |
| Crear solicitud estándar | Solicitud `REP-2026-A9DF0017`, 20/11/2026 09:00, estado `Pendiente`, visible en «Mis avisos». |
| Consultar el detalle | Servicio, avería, dirección, contacto e historial inicial coinciden con lo introducido. |
| Administrar catálogo y técnico | Servicio «Cerrajería QA» y técnico compatible creados mediante sus formularios. |
| Asignar desde `/admin` | Mensaje de éxito; la solicitud pasó a `Asignada` y mostró «Técnico QA Cerrajería». |
| Revisar `/admin/calendario` | La cita aparece en la vista mensual de noviembre. |
| Iniciar sesión como técnico | `/tecnico` muestra la reparación asignada, y su detalle reconoce al técnico. |
| Avanzar el trabajo | `Asignada → En curso → Finalizada`, ambos pasos confirmados por pantalla. |
| Comprobar historial | Cuatro eventos ordenados: alta del cliente, asignación del administrador, inicio y finalización por el técnico; aparecen los actores. |
| Intentar otro avance | En `Finalizada` ya no aparece botón para cambiar de estado. |
| Volver a iniciar como cliente | La solicitud se muestra con estado `Finalizada` en «Mis avisos». |

**Separación de roles comprobada en sesión autenticada:** el técnico recibe HTTP `403` al consultar `/admin`, `/solicitar` y `/mis-avisos`; el cliente recibe HTTP `403` en `/admin`, `/tecnico` y `/tecnico/aviso?id=1`. Un técnico obtiene `404` al consultar un aviso inexistente. Estas pruebas no sustituyen una auditoría de seguridad.

**Regresión automatizada:** el script `entorno-p2/probar-integracion-aislada.ps1` terminó con código de salida `0` después de comprobar los módulos de integración, catálogo y rutas HTTP (batería de 87 casos documentada anteriormente). Las otras 68 pruebas automatizadas (registro, solicitudes y edición) ya constaban como aprobadas en `VALIDACION-DOCKER-NAVEGADOR-20261108.md`; **no se repitieron en esta revisión**.

## Cierre del entorno y límites

- Se cerró únicamente la pestaña temporal de `8084`; se verificaron las **ocho pestañas originales** abiertas y conservadas.
- Se ejecutó `docker compose -f .\entorno-p2\compose.yaml down -v`: eliminó los dos contenedores, la red y el volumen MySQL del **proyecto temporal creado para esta sesión**. Se borró su `.env` de credenciales de prueba. La verificación posterior mostró **cero** contenedores y **cero** volúmenes etiquetados con ese proyecto.
- El proyecto Docker original permaneció activo: `http://127.0.0.1:8083/` respondió **HTTP 200** después de limpiar las pruebas.
- No se enviaron datos reales, correos, mensajes a Paula, entregas a Canvas ni cambios al repositorio remoto. No hay validación de AWS, despliegue público, concurrencia intensiva o pruebas de dispositivos físicos.

**Conclusión:** prioridades 2 y 4 comprobadas satisfactoriamente para el escenario de escritorio y el flujo funcional descrito. La rama local está preparada para una revisión final de textos/diseño y, solo por decisión posterior de Erick, una eventual publicación o integración.
