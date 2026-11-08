# ReparaYa P2 — Validación Docker y navegador (08/11/2026)

## Alcance y aislamiento

- Trabajo realizado solo en el worktree `Producto-2-integracion-local`, rama `feat/integracion-paula-local`.
- Se levantó por primera vez el proyecto Compose `reparaya-p2-integracion-local`, usando MySQL y volumen propios, imagen web propia y `127.0.0.1:8084` (el original utiliza `8083`).
- Solo se utilizaron cuentas, reparaciones, importes y direcciones inventados. No se conectó a AWS, Canvas, Sites ni GitHub remoto.
- Antes de arrancar, no existían contenedores ni volúmenes con el nombre del nuevo proyecto Compose.

## Baterías ejecutadas

1. `probar-flujo-p2.ps1`: **22/22**. Registro, validación de datos, acceso, perfil, credenciales, cierre de sesión y verificación de hash en MySQL.
2. `probar-solicitudes-p2.ps1`: **20/20**. Altas de reparaciones, urgencia, consulta privada, cancelación, permisos e historial en MySQL.
3. `probar-edicion-solicitudes-p2.ps1`: **26/26**. Edición, escape de HTML, bloqueo por plazo/estado, permisos de otro cliente e historial en MySQL.
4. `probar-integracion-aislada.ps1`: **87/87**. Las 20 de equipo, 25 del catálogo de servicios/técnicos y 42 HTTP, en contenedor efímero diferente y sin puertos.

**Total de comprobaciones automatizadas pasadas en esta sesión: 155/155.** No es una garantía de ausencia de fallos ni una auditoría de seguridad completa.

Los tres scripts antiguos inicialmente fallaron en su comprobación SQL final porque buscaban `compose.yaml` desde la carpeta de invocación. Se corrigieron exclusivamente en este worktree con `docker compose -f (Join-Path $PSScriptRoot 'compose.yaml') exec ...`; después se repitieron y aprobaron íntegros. No era un fallo de la aplicación web.

## Recorrido manual con navegador

1. `/` responde y muestra la página introductoria del laboratorio.
2. `/login`: inicio de sesión con cliente ficticio y entrada a su perfil.
3. Promoción de esa cuenta exclusivamente en la BD de pruebas para simular un administrador; `/admin` muestra reparaciones inventadas y enlaces a calendario, servicios y técnicos.
4. `/admin/servicios`: alta visual de «Cerrajería (DEMO navegador)», 45,00 €; se confirmó su aparición activa en el catálogo.
5. `/admin/tecnicos`: alta de un técnico ficticio especializado en electricidad; se verificó nombre, correo, especialidad y disponibilidad en el listado.
6. `/admin`: el técnico apareció como opción compatible y fue asignado a una reparación pendiente; la interfaz confirmó `Asignada`.
7. `/admin/calendario`: vista mensual mostró esa reparación asignada y el resto de citas ficticias.
8. Tras cerrar sesión, se inició como el nuevo técnico: `/tecnico` mostró solo su reparación. Desde el detalle se cambió `Asignada → En curso → Finalizada`, y el historial reflejó actor y secuencia; al finalizar ya no se ofrece otro avance.

## Observaciones y límites

- **Funcionalidad probada:** en los recorridos anteriores no aparecieron errores de aplicación.
- **Interfaz pendiente:** pantallas con HTML de laboratorio, apenas estilizadas; la portada no ofrece navegación visible al resto de la aplicación. No considerarlas una interfaz final de entrega o producto comercial.
- **Consola del navegador:** único error recogido: `/favicon.ico` ausente (404), cosmético.
- No se han probado en dispositivos físicos distintos, con lectores de pantalla, en concurrencia intensiva ni con AWS.
- No se ha fusionado con `main` ni se ha publicado el desarrollo. Se conserva la atribución al trabajo de Erick y Paula.

## Cierre

- Pestaña temporal `127.0.0.1:8084` cerrada; comprobado que se conservan las ocho pestañas previas.
- `docker compose down -v` ejecutado **solo** sobre `reparaya-p2-integracion-local`: eliminados sus dos contenedores, su volumen MySQL y su red, creados en esta misma prueba; listados posteriores de contenedores y volúmenes sin restos de ese proyecto.
- Archivo `entorno-p2/.env` de credenciales ficticias aleatorias, generado para esta sesión, eliminado; se puede recrear ejecutando `preparar-env-local.ps1`.
- Proyecto Docker original `reparaya-p2-erick` conservado; `http://127.0.0.1:8083/` respondió **HTTP 200** tras la limpieza.
- Rama `main` sin cambios en código; su informe de auditoría anterior continúa como archivo sin seguimiento. La rama aislada de integración conserva los cambios y pruebas documentadas.
