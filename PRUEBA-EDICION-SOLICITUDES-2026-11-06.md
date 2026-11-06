# ReparaYa P2 — Edición controlada de datos de solicitudes (06/11/2026)

## Resultado verificado

**26/26 PASS, salida 0**, ejecutando realmente el script
`entorno-p2/probar-edicion-solicitudes-p2.ps1` con **Windows PowerShell 5.1**.
El comprobador PHP `tests/comprobar-edicion-ficticia.php` consultó **MySQL real** e informó
`EDICION_Y_HISTORIAL_BD=PASS`. Se repitieron después las suites previas de usuarios y solicitudes,
que continuaron con **22/22 PASS** y **20/20 PASS** (ambas con salida 0).
En total hay **68 pruebas automatizadas que pasan**; esto no equivale a los cuatro CRUD entregados.

**Reproducción de Erick:** el 06/11 compartió personalmente la salida íntegra de sus propias
ejecuciones anteriores: `TOTAL_PRUEBAS=22/22`,
`REGISTRO_CIFRADO_Y_ROL=PASS`, `TOTAL_PRUEBAS_SOLICITUDES=20/20` y
`REGLAS_ESTADOS_HISTORIAL=PASS`. Es una **transcripción de consola del estudiante** recibida
en la conversación, no una captura PNG archivada. Esta ampliación de 26 casos fue creada y
ejecutada por el asistente; Erick todavía debe practicarla y revisarla.

## Qué se incorporó exactamente

- `GET /aviso/editar?id=…`: abre el formulario solo si el aviso pertenece al cliente autenticado,
  está `Pendiente` y faltan **más de 48 horas** para la cita.
- `POST /aviso/editar`: permite cambiar **descripción de avería, dirección y teléfono**;
  usa CSRF y validación de longitud del servidor. No permite cambiar servicio, fecha ni urgencia.
- `Solicitud::actualizarDatosCliente()`: consulta el aviso con `FOR UPDATE`, comprueba
  titularidad/estado/plazo dentro de una transacción y actualiza únicamente los tres campos.
  Si hubo modificación, inserta un evento en `historial_estados` con estado
  `Pendiente → Pendiente` y observación de edición. Si no hubo cambios, no duplica el historial.
- `detalle-aviso.php`: muestra el enlace de edición solo cuando corresponde y presenta
  las observaciones del historial con `htmlspecialchars`.

El plazo de **48 horas para editar** se adoptó como una regla conservadora de este prototipo
para no modificar reservas próximas. **No afirmar que el profesor exigió expresamente esta
regla para editar**: la regla de 48 horas comprobada en la actividad se refería a solicitudes
estándar y cancelación.

## Archivos y pruebas

| Archivo | Papel |
| --- | --- |
| `erick-lab-01/public/index.php` | Enruta GET/POST de edición y rechaza otros métodos |
| `erick-lab-01/app/Controllers/SolicitudController.php` | Comprueba autenticación, entradas y CSRF |
| `erick-lab-01/app/Models/Solicitud.php` | Transacción SQL con bloqueo y registro de cambios |
| `erick-lab-01/app/Views/editar-aviso.php` | Vista de edición con valores pre-rellenados |
| `erick-lab-01/app/Views/detalle-aviso.php` | Enlace visible cuando se permite y observaciones escapadas |
| `erick-lab-01/tests/comprobar-edicion-ficticia.php` | Relectura SQL real de edición e historial |
| `entorno-p2/probar-edicion-solicitudes-p2.ps1` | 26 comprobaciones HTTP, compatible con PS 5.1 y 7 |

Se comprobaron alta/login de dos clientes, edición válida, CSRF ausente (403), datos inválidos
(422), cambio real (303), salida HTML escapada, aviso urgente próximo (409), aviso cancelado
(409), acceso de otro cliente (404), método no admitido (405) y lectura SQL final.
No se usa SQLite ni tablas simuladas.

El SQL original del docente **no fue modificado**. Las ampliaciones P2 previamente aprobadas
continúan en archivos separados y en el volumen aislado; no se añadieron nuevas migraciones.
No se tocó Docker de P1 ni se ejecutó `down -v`.

## Cómo repetir en el PC (solo datos inventados)

```powershell
Set-Location 'C:\Users\guill\Documents\UOC\FP448-Backend-PHP-MVC\Producto-2\entorno-p2'
docker compose ps
.\probar-edicion-solicitudes-p2.ps1
```

Esperado al final: `EDICION_Y_HISTORIAL_BD=PASS` y `TOTAL_PRUEBAS_EDICION=26/26`.
Cada ejecución agrega **dos usuarios y dos incidencias de ensayo** en la BD persistente P2.
No borrar volúmenes para limpiarlos ni usar información real de clientes.

## Honestidad académica y próximos pasos

La implementación y el test nuevo los escribió el asistente. Erick validó directamente en su
consola los 42 casos anteriores, pero todavía no ha revisado personalmente esta edición;
la guía `erick-lab-01/GUIA-UOCSTUDY-07.md` propone ejercicios para comprenderla.
Faltan código/revisión conjunta con Paula, roles admin/técnico, servicios, asignaciones,
calendario, repositorio Git compartido, AWS, Word, mapa a mano, vídeo, capturas nuevas y entrega.
**No marcar Producto 2 como terminado ni publicarlo.**
