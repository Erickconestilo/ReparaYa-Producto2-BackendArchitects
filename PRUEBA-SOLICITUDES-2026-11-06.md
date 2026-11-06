# ReparaYa P2 — Solicitudes, historial, urgencia y regla de 48 horas

**Fecha:** 06/11/2026. **Entorno:** `entorno-p2`, MySQL 8 y PHP 8.2 locales, accesibles solo desde `127.0.0.1:8083`. **Autor de la implementación de demostración:** asistente de IA, bajo autorización de Erick; Erick deberá revisarla y defender personalmente sus decisiones antes de entregarla.

**Compatibilidad de la prueba en el PC de Erick:** tras corregir `Add-Type -AssemblyName System.Net.Http` y la codificación UTF-8 BOM del `.ps1`, se repitió con el ejecutable original de **Windows PowerShell 5.1** y obtuvo **20/20 PASS, exit 0**. La prueba paralela de usuarios obtuvo **22/22 PASS, exit 0**. El primer error `HttpClientHandler` provenía del script de pruebas, no de Docker ni de PHP. Cada ejecución añade exclusivamente datos ficticios al entorno P2 persistente.

## Esquema: conservar el profesor, ampliar en archivos independientes

El SQL oficial sigue exactamente igual, SHA-256 `C517DC46D14E6F3A6FA27C5DD2B7849C0DCA01350151E54D9C3BDA79E1AADB4B`.

Las ampliaciones se aplicaron **solo** a la base `reparaya_p2` del entorno aislado:

1. `migraciones/20261106_01_ampliacion_p2.sql`: conserva las cuatro tablas, amplía `incidencias.localizador` de 12 a 20 caracteres, añade `franja_horaria` y `telefono_contacto`, incluye el estado `En curso`, agrega `precio_base` a `especialidades` y crea `historial_estados` con actor, estado anterior/nuevo, marca temporal y claves externas.
2. `migraciones/20261106_02_datos_demo.sql`: añade dos especialidades **inventadas** para probar (Fontanería DEMO = 55,00 €, Electricidad DEMO = 65,00 €). No son servicios ni precios aprobados por Paula.
3. `migraciones/20261106_03_normalizar_enum_urgencia.sql`: corrige la codificación del ENUM `Estándar` que introdujo el importador automático de MySQL, sin cambiar el original del profesor.

**Advertencia:** la migración `01` es de aplicación única y ya se ejecutó en esta BD persistente. No volver a lanzarla sobre el volumen actual. Para integrar con Paula hay que revisar primero sus tablas y elegir una única secuencia compartida de migraciones; no editar su código ni copiar datos de P1.

## Incidencia real de codificación y cómo se detectó

En el archivo SQL oficial aparece correctamente `Estándar` (UTF-8). Sin embargo, al inicializar el volumen con la importación automática de MySQL, `SHOW COLUMNS` devolvió `enum('EstÃ¡ndar','Urgente')` (`HEX` del texto: `...c383c2a1...`).

En la primera ejecución del test HTTP, la solicitud urgente devolvió 303 (correcta), pero una estándar con fecha a siete días devolvió **500**. Se comprobó el esquema real y se concluyó que el acento se había almacenado mal. Se corrigió con `SET NAMES utf8mb4` y `ALTER TABLE` en `migraciones/20261106_03_normalizar_enum_urgencia.sql`, y se verificó `enum('Estándar','Urgente')` con bytes `...c3a1...`. Después, el mismo test alcanzó **20/20 PASS**.

Este caso es un buen ejemplo para la revisión crítica de IA de la memoria: una propuesta que aparentaba funcionar fue insuficiente hasta probar datos acentuados. No atribuir la investigación/corrección al trabajo manual de Erick si todavía no la ha repetido.

## Código y reglas realmente implementadas

- `erick-lab-01/app/Models/Solicitud.php`: `SELECT`/`INSERT`/`UPDATE` preparados; creación de incidencia + historial inicial en una transacción; cancelación bajo `SELECT ... FOR UPDATE` y transacción; consultas filtradas por `cliente_id`.
- `erick-lab-01/app/Controllers/SolicitudController.php`: acceso únicamente para clientes con sesión y rol `particular`; comprobación CSRF; validación de campos y especialidad; fechas en zona `Europe/Madrid`.
- `erick-lab-01/app/Views/solicitar.php`, `mis-avisos.php`, `detalle-aviso.php`: formulario, lista y detalle; salidas escapadas con `htmlspecialchars()`.
- `erick-lab-01/public/index.php`: nuevas rutas GET/POST sin perder inicio, registro ni login.
- La franja seleccionada se traduce a **09:00** (`manana`) o **16:00** (`tarde`) como simplificación demostrativa; no es un calendario de disponibilidad real. Las fechas estándar deben distar al menos **48 horas**, las urgentes pueden estar más cerca pero nunca en el pasado.
- Solo se permite cancelar solicitudes propias en estado `Pendiente` o `Asignada` cuando faltan **más** de 48 horas; la cancelación escribe un evento en `historial_estados`. Las solicitudes terminadas/canceladas no vuelven atrás por este módulo.

## Prueba verificable

Desde `entorno-p2`:

```powershell
.\probar-solicitudes-p2.ps1
```

**Resultado real 06/11:** `TOTAL_PRUEBAS_SOLICITUDES=20/20` y `REGLAS_ESTADOS_HISTORIAL=PASS` (MySQL). Comprueba dos cuentas distintas, creación de estándar/urgente, regla de 48 horas, historial, cancelación y prohibición de consultar/cancelar avisos ajenos.

En una ejecución anterior superó **14/14**; se añadieron seis casos más, incluyendo separación entre dos clientes. Los resultados se contrastaron con datos de verdad en MySQL, no con tablas simuladas. El test deja cuentas e incidencias inventadas en la base de desarrollo; no usar datos reales.

**Persistencia:** tras reiniciar únicamente `reparaya-p2-erick-db-1`, `INCIDENCIAS_ANTES=3` y `INCIDENCIAS_DESPUES=3`, `HISTORIAL_ANTES=4` y `HISTORIAL_DESPUES=4`. Corresponde al punto temporal de la prueba anterior de 14 casos; en ejecuciones posteriores se agregaron nuevas filas ficticias. P1 y su contenedor de base siguieron activos.

## Lo que no demuestra

No se han probado/implementado aquí el calendario mensual/semanal/diario, asignación por administrador, transiciones técnico `Asignada → En curso → Finalizada`, alta/baja de técnicos y servicios, permisos completos admin/técnico, precio base consensuado, Git colaborativo, AWS, Word/vídeos ni la participación de Paula. El módulo de solicitudes es un **primer recorrido de cliente**, no el CRUD2 integrado y terminado según la rúbrica. No marcar su tarjeta `Hecho` ni entregar todavía.

**Capturas:** todavía no hay PNG nuevo de este bloque en `Doc_Captures_Print`. Las salidas de pruebas pueden repetirse, pero las capturas deben revisarse y guardarse sin credenciales; el material anterior de Erick sigue preservado.
