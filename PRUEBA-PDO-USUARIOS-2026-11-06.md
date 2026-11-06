# ReparaYa P2 — Evidencia técnica de PHP, PDO y usuarios (06/11/2026)

## Qué existe y quién hizo qué

- **Erick** escribió previamente el laboratorio inicial, `InicioController.php`, el formulario `registro.php` y las rutas GET, con capturas E02–E11.
- El **asistente de IA**, por autorización de Erick para avanzar y explicarlo después, creó `entorno-p2` y añadió la conexión, modelo y controlador de usuarios, rutas POST, login y perfil. No presentar este código nuevo como escrito personalmente por Erick hasta que él lo revise y practique.
- Se conservó el SQL original del docente, SHA-256 `C517DC46D14E6F3A6FA27C5DD2B7849C0DCA01350151E54D9C3BDA79E1AADB4B`.

## Entorno y prueba ejecutada en el PC

- Compose aislado `reparaya-p2-erick` (MySQL 8.0 más servidor PHP 8.2 del equipo); base `reparaya_p2`, cuatro tablas iniciales oficiales y volumen propio `reparaya-p2-erick_datos_mysql_p2`.
- MySQL no publica puertos. PHP publica solo `http://127.0.0.1:8083` en el equipo local. La base de Producto 1 (`reparaya-p1-db-1`) quedó funcionando y sin cambios.
- Variables `DB_*` llegan al contenedor PHP desde `.env` de `entorno-p2`, creado mediante `preparar-env-local.ps1` con dos claves aleatorias. **No copiar ni entregar `.env`**: está ignorado por `.gitignore`.
- `php -l`: comprobados los ocho archivos de la aplicación más el test CLI, todos sin errores de sintaxis.
- Conexión PDO real: `SELECT 1 = 1` y `SHOW TABLES` detectó **4 tablas**; no es una simulación.
- Comprobación funcional `entorno-p2/probar-flujo-p2.ps1`: **22/22 PASS** el 06/11. Incluye:
  - `GET /`, `GET /registro`, `GET /login` y ruta 404;
  - CSRF ausente → 403; datos inválidos → 422;
  - alta válida → 303; correo duplicado → 409;
  - contraseña equivocada → 401; login válido → 303;
  - perfil anónimo → 302; perfil autenticado → 200;
  - editar nombre y teléfono → 303 y releerlos en MySQL;
  - cambio de correo/contraseña exige clave antigua, rechaza la incorrecta y valida el acceso nuevo;
  - logout → 303 y acceso posterior al perfil → 302;
  - consulta CLI `tests/comprobar-usuario-ficticio.php`: hash almacenado distinto de la clave de prueba, `password_verify()` correcto, rol `particular`.
- Se usan cuentas y contraseñas **inventadas**, con dominio `example.test`. Las respuestas no revelan el hash ni la contraseña.
- Persistencia: antes y después de reiniciar **solo** `reparaya-p2-erick-db-1`, `SELECT COUNT(*) FROM usuarios` devolvió **4 y 4** (cuatro registros ficticios creados durante estas pruebas, incluidos dos intentos previos del test). La base volvió a estado `healthy`.

## Dificultad real y corrección de IA

La primera versión del script de pruebas usaba `Invoke-WebRequest -MaximumRedirection 0`; PowerShell 7 falló al recibir un `303` correcto (límite de redirecciones). No se confundió con un fallo del registro PHP. El script se corrigió para usar `HttpClient` con `AllowAutoRedirect = false`, se repitió y alcanzó **22/22**. Este ejemplo se puede analizar críticamente en la memoria de uso de IA; la corrección la realizó el asistente, no atribuirla como modificación personal de Erick.

**Segunda incidencia, detectada por Erick en su consola:** Windows PowerShell 5.1 no encontraba el tipo `System.Net.Http.HttpClientHandler`; no era un error del servidor. Se añadió `Add-Type -AssemblyName System.Net.Http -ErrorAction Stop` al inicio de ambos scripts y se guardaron como **UTF-8 con BOM** (necesario en 5.1 para no alterar `Estándar`). Verificación ejecutada **realmente con Windows PowerShell 5.1**: `probar-flujo-p2.ps1` **22/22 PASS, exit 0** y `probar-solicitudes-p2.ps1` **20/20 PASS, exit 0**. PowerShell 7 sigue siendo compatible. Las ejecuciones generan usuarios y solicitudes de ensayo en MySQL P2, sin tocar el SQL original.

## Alcance y pendientes honestos

**Logrado en local:** registro, login, cierre de sesión, validación en servidor, contraseña cifrada, sesión, perfil y edición de correo/contraseña/nombre/teléfono. Estas pruebas contribuyen a los criterios «Base de datos» y «Registro-Login y Perfil», **sin cerrar aún su rúbrica**.

**No implementado ni verificado:** gestión administrativa de usuarios, vistas dinámicas completas por rol, cuatro CRUD integrados, incidencias y sus 48 horas, asignación, transición/historial, calendario, precio base, pruebas de roles admin/técnico, Git remoto y aportaciones verificables de ambos, despliegue AWS, capturas PNG nuevas, Google Sites, Word, mapa manuscrito, vídeos y entrega en Canvas. Paula todavía no ha compartido su código en esta carpeta.

## Repetir la prueba (Windows, PowerShell)

```powershell
Set-Location 'C:\Users\guill\Documents\UOC\FP448-Backend-PHP-MVC\Producto-2\entorno-p2'
docker compose ps
.\probar-flujo-p2.ps1
```

Cada ejecución añade **una cuenta ficticia** nueva al volumen P2. No usar correos ni contraseñas personales. Para ver la app, `http://127.0.0.1:8083/registro`, `http://127.0.0.1:8083/login` y `http://127.0.0.1:8083/perfil` (este último necesita sesión).

**No ejecutar** `docker compose down -v`: borraría la base de desarrollo P2. `docker compose stop` solo detiene sus contenedores, sin quitar el volumen; `docker compose up -d` los vuelve a iniciar. Todo ello debe ejecutarse **desde esta carpeta `entorno-p2`**, no desde P1, Supabase ni otro proyecto.

## Evidencia académica pendiente

Las salidas de consola y el código son verificables; **todavía no hay PNG nuevo de estas 22 pruebas** en `Doc_Captures_Print`. Erick debe revisar una sesión real, tomar la captura sin secretos y comprender cómo repetir las pruebas antes de afirmar que puede defenderlo individualmente. La historia de Git y la integración en Sites aún deben realizarse.
