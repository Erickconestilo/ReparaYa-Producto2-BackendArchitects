# UocStudy 06 — Solicitudes de reparación en PHP MVC

**Meta:** entender y comprobar por qué una solicitud urgente puede realizarse con menos antelación, cómo se aplican las 48 horas y por qué se guarda un historial de cambios. Entorno real en `http://127.0.0.1:8083` y SQL original del profesor + migraciones aparte. Implementación inicial hecha por el asistente a petición de Erick, pendiente de práctica individual.

## Lectura mínima (10–15 minutos)

1. [Enunciado oficial P2](https://aula.uoc.edu/courses/91603/assignments/989195): leer solo «Tipo de servicio», «Cancelación», «Seguimiento del estado», «Historial de estados» y «CRUD Incidencias (Clientes)». El requisito de calendario y técnico corresponde a integración con Paula.
2. [Transacciones PDO](https://www.php.net/manual/es/pdo.begintransaction.php): descripción de `beginTransaction()` y ejemplo básico. [PDO `commit`](https://www.php.net/manual/es/pdo.commit.php) y [PDO `rollBack`](https://www.php.net/manual/es/pdo.rollback.php): qué pasa si una de dos escrituras falla.
3. [Fechas PHP](https://www.php.net/manual/es/class.datetimeimmutable.php): para qué sirve `DateTimeImmutable`; [zonas horarias](https://www.php.net/manual/es/class.datetimezone.php): por qué usamos `Europe/Madrid`.

Los materiales MVC de UOC son vídeos, no páginas verificables; no inventar paginación.

## Misión A — Sigue una solicitud de extremo a extremo

En VS Code:

- `public/index.php`: busca `POST /solicitar`.
- `app/Controllers/SolicitudController.php`: busca `crear()` y la condición de `48 * 3600` segundos.
- `app/Models/Solicitud.php`: busca `INSERT INTO incidencias` y el siguiente `INSERT INTO historial_estados` dentro de una transacción.
- `app/Views/mis-avisos.php`: busca `foreach ($avisos as $aviso)`.

**Qué significa:** la ruta recibe, el controlador valida, el modelo guarda, la vista muestra. La base mantiene el estado actual y el historial para poder explicar quién cambió qué y cuándo.

**Práctica breve:** abre `http://127.0.0.1:8083/registro`, crea cuenta con correo `@example.test` inventado y contraseña ficticia distinta de las personales. Entra en `/perfil` → «Solicitar reparación». Prueba estándar con fecha mañana (debe rechazar) y urgente con fecha mañana (debe aceptar). No emplees direcciones o teléfonos reales.

## Misión B — Explica la cancelación

En `Solicitud::cancelarCliente()` localiza:

1. `WHERE id = :id AND cliente_id = :cliente` — impide acceder a avisos ajenos.
2. `estado` debe ser `Pendiente` o `Asignada` — impide cancelar un aviso finalizado.
3. `<= 48 * 3600` — rechaza cancelar cuando ya faltan **48 horas o menos**.
4. `FOR UPDATE`, `beginTransaction()`, `commit()` — conserva la coherencia entre estado actual y entrada de historial.

**Pregunta para practicar:** ¿por qué no basta esconder el botón «Cancelar» en el HTML? Porque una petición POST podría enviarse manualmente: la regla tiene que comprobarse también en el servidor.

## Misión C — Comprueba de verdad

En PowerShell:

```powershell
Set-Location 'C:\Users\guill\Documents\UOC\FP448-Backend-PHP-MVC\Producto-2\entorno-p2'
docker compose ps
.\probar-solicitudes-p2.ps1
```

Esperado: `20/20 PASS` y `REGLAS_ESTADOS_HISTORIAL=PASS`. El script crea cuentas y solicitudes ficticias dentro de MySQL P2, y es reproducible. Si falla, conservar el mensaje concreto; no borrar el volumen de MySQL para «arreglarlo».

## Evidencias y límites

- Informe real: `../PRUEBA-SOLICITUDES-2026-11-06.md`.
- Debes guardar capturas revisadas de formulario, lista, historial y terminal **sin credenciales**; aún no están guardadas.
- Site/Trello pendientes de coordinación con Paula, sin modificar su parte.
- Rúbrica: contribuye a «Base de datos» (2 puntos) y «Gestión de incidencias y calendario» (5 puntos), pero **no los cierra**: faltan administración, técnico, transiciones completas, calendario e integración.
- Próxima sesión: solicitar a Paula el repositorio/código real, revisar conjuntamente las migraciones y probar el ciclo completo `Pendiente → Asignada → En curso → Finalizada` con tres roles.
