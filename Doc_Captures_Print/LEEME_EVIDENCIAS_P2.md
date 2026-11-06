# FP.448 · ReparaYa · Producto 2 · Capturas y evidencias

**Carpeta creada por el estudiante.** Este índice organiza capturas, no certifica funcionalidades realizadas ni convierte automáticamente una práctica en un requisito entregado.

**ACTUALIZACIÓN REAL 28/10/2026 — CAPTURAS YA GUARDADAS EN WINDOWS:** Se localizaron los archivos originales en `C:\Users\guill\Pictures\Screenshots`, se copiaron **nueve PNG** a esta carpeta (incluida E11, formulario accesible) y se verificó que cada copia tiene el **mismo SHA-256** que su original. Los estados de «pendiente de descargar» en los registros históricos describen el momento anterior a esta recuperación y quedan sustituidos por la tabla actualizada. Las dos capturas de 24/10 (E01, E03) todavía no se han recuperado.

**NUEVA PRUEBA TÉCNICA 06/11 (AÚN SIN PNG):** El asistente implementó una conexión MySQL P2 separada, registro, login, sesión y perfil sobre el SQL original. Prueba CLI/HTTP reproducible: `../entorno-p2/probar-flujo-p2.ps1` → **22/22 PASS**; PDO `SELECT 1` y cuatro tablas; datos persistentes tras reiniciar solo P2. Informe: `../PRUEBA-PDO-USUARIOS-2026-11-06.md`; guía de aprendizaje: `../erick-lab-01/GUIA-UOCSTUDY-05.md`. **No añadir imágenes inexistentes al inventario**: las nueve PNG siguen siendo las históricas, las capturas nuevas se deben obtener en una sesión real revisada por Erick sin mostrar claves ni `.env`.

**SEGUNDO BLOQUE 06/11 (TAMBIÉN SIN PNG NUEVA):** `../entorno-p2/probar-solicitudes-p2.ps1` → **20/20 PASS**. Casos de 48 horas, urgente/estándar, «Mis avisos», historial, cancelación y separación entre dos clientes reales de prueba. Documento: `../PRUEBA-SOLICITUDES-2026-11-06.md`; guía: `../erick-lab-01/GUIA-UOCSTUDY-06.md`. La corrección comprobada de codificación `Estándar` está registrada, igual que el fallo previo. No atribuir al estudiante el código escrito por IA; quedan pendientes capturas y práctica personal. **Nueve PNG históricas siguen conservadas**, sin edición de Google Sites ni entrega.

**TERCER BLOQUE 06/11 (CONSOLAS, NO PNG):** Erick pegó personalmente en el chat los resultados de las dos suites previas: **22/22** de usuarios con bcrypt y **20/20** de solicitudes con MySQL real. El asistente añadió y probó el flujo de actualización de datos de una solicitud: `../entorno-p2/probar-edicion-solicitudes-p2.ps1` → **26/26 PASS** con PowerShell 5.1 y comprobador SQL real `EDICION_Y_HISTORIAL_BD=PASS`. Las dos suites previas siguen pasando tras los cambios (**68/68 pruebas**). Documentado en `../PRUEBA-EDICION-SOLICITUDES-2026-11-06.md` y `../erick-lab-01/GUIA-UOCSTUDY-07.md`. La transcripción de consola no equivale a una captura de pantalla; **las PNG nuevas siguen pendientes**. No se atribuye al estudiante la programación añadida por IA.

## Dónde trabajar

- Código del primer laboratorio de Erick: `../erick-lab-01/`
- Evidencias del Producto 2: esta carpeta `Doc_Captures_Print/`
- Guía didáctica: `../erick-lab-01/GUIA-UOCFLOW-01.md`
- Enunciado académico: https://aula.uoc.edu/courses/91603/assignments/989195

## Capturas propuestas para la primera sesión

| Archivo sugerido | Qué mostrar en pantalla | Estado inicial | Valor para P2 |
| --- | --- | --- | --- |
| `P2_Erick_01_Docker_comprobacion.png` | PowerShell con `docker --version`, `docker compose version` y `docker info --format '{{.ServerVersion}}'` | **Captura recibida en el chat y revisada el 24/10/2026; pendiente de descargar/guardar en esta carpeta local** | Preparación del entorno, no demuestra CRUD |
| `P2_Erick_02_Estructura_MVC_VSCode.png` | VS Code con carpetas `public`, `app/Controllers`, `app/Views` y el archivo `InicioController.php` | **PNG ORIGINAL GUARDADO EN WINDOWS**; SHA verificado | Explicación inicial de la arquitectura MVC |
| `P2_Erick_03_PHP_sintaxis.png` | PowerShell mostrando `No syntax errors detected` para los 3 PHP | **Captura íntegra recibida, revisada y preparada para descargar desde esta conversación el 24/10; falta guardarla en esta carpeta local del PC** | Prueba del código PHP inicial |
| `P2_Erick_04_Pagina_inicio.png` | Navegador en `http://127.0.0.1:8082/` con el texto que Erick modificó personalmente | **PNG ORIGINAL GUARDADO EN WINDOWS**; captura válida del funcionamiento en ese momento; SHA verificado | Prueba funcional del recorrido navegador → controlador → vista, no un CRUD |
| `P2_Erick_05_Ruta_404.png` | Navegador en `http://127.0.0.1:8082/no-existe` con respuesta de página no encontrada | Pendiente de guardar | Primera prueba de ruta inexistente |
| `P2_Erick_07_Ruta_asteriscos_404.png` y `P2_Erick_07_Ruta_asteriscos_404_recorte.png` | Navegador con `http://127.0.0.1:8082/**` y `404 - Página no encontrada`, en versión completa y recortada | **AMBOS PNG ORIGINALES GUARDADOS EN WINDOWS**; SHA verificados | Evidencia de ruta errónea y diagnóstico, no fallo del código |
| `P2_Erick_08_Contenedor_Activo_Terminal.png` | Terminal de VS Code con recuperación de Docker (`29.7.2`), comando de arranque y `docker ps` mostrando el contenedor `Up` | **PNG ORIGINAL GUARDADO EN WINDOWS**; SHA verificado | Prueba de motor activo y servidor Docker levantado, no prueba del formulario ni del CRUD |
| `P2_Erick_09_Vista_registro_codigo.png` | VS Code con `app/Views/registro.php`, formulario con nombre, correo y contraseña | **PNG ORIGINAL GUARDADO EN WINDOWS**; SHA verificado, sintaxis PHP pasó | Vista de formulario creada por Erick; ruta GET y registro funcional aún pendientes |
| `P2_Erick_06_Incidencia_Docker_Desktop_cerrado.png` | VS Code con controlador editado y terminal mostrando `failed to connect to the docker API ... dockerDesktopLinuxEngine` | **PNG ORIGINAL GUARDADO EN WINDOWS**; SHA verificado | Diagnóstico y recuperación del entorno; NO es error de PHP ni evidencia de CRUD |
| `P2_Erick_10_Controlador_mostrarRegistro_PHP_OK.png` | VS Code mostrando `mostrarRegistro()` junto a `mostrar()` y comprobación `php -l` sin errores | **PNG ORIGINAL GUARDADO EN WINDOWS**; SHA verificado contra captura subida por Erick | Método de controlador creado personalmente; no acredita todavía la ruta GET ni persistencia |
| `P2_Erick_11_Formulario_registro_GET_200.png` | Navegador con URL `127.0.0.1:8082/registro`, encabezado «Crear cuenta en ReparaYa» y campos nombre, correo y contraseña | **PNG ORIGINAL GUARDADO EN WINDOWS**; 143068 bytes; SHA-256 `4bb3f892a34d8bbab9c46bb4ad48d8bb413d1b620e03476b62dd8885ea5dd72e` coincide con captura automática del 28/10 a las 17:15:06 | Comprobación funcional de la ruta GET y vista, NO de registro ni persistencia |

## Registro de cada captura

Cuando llegue una captura real, anotar fecha, comando/acción realizada, resultado observado, nombre exacto del archivo y vínculo con el criterio de rúbrica. Atribuir ejecución a Erick o Paula solo cuando exista evidencia o confirmación. No afirmar que ya se capturó nada que no esté guardado.

### Registro verificado 24/10/2026 — Erick — Evidencia 01

- Se ha recibido en esta conversación una **captura real** de PowerShell desde `Producto-2/erick-lab-01`.
- `docker --version` muestra **Docker 29.7.2** (instalación).
- `docker compose version` muestra **Docker Compose v5.4.0** (herramienta disponible).
- `docker info --format '{{.ServerVersion}}'` muestra **29.7.2** (motor en marcha).
- Entre dos pruebas Erick tecleó por error el texto de una respuesta anterior como comando (`Docker version 29.7.2, build a7dcaa6`); generó el error esperado de uso, **no invalidó** las comprobaciones correctas posteriores. Mantener captura auténtica.
- La imagen se ha preparado como descarga con el nombre `P2_Erick_01_Docker_comprobacion.png`; **todavía no se ha verificado que exista en esta carpeta del ordenador del usuario**, por lo que la siguiente acción es guardarla aquí.
- Alcance del criterio: entorno operativo; no demuestra CRUD, MVC completo ni base de datos importada.

### Registro de avance 24/10/2026 — Erick — PHP sintaxis (3/3)

- Erick ejecutó personalmente en PowerShell, desde `Producto-2/erick-lab-01`, el comando de verificación `docker run --rm -v "${PWD}:/lab" -w /lab --entrypoint php reparaya-p1-web:latest -l public/index.php`.
- La salida que compartió fue: `No syntax errors detected in public/index.php`.
- Estado: **comprobación de sintaxis de `public/index.php` superada**. Erick también ejecutó personalmente `docker run --rm -v "${PWD}:/lab" -w /lab --entrypoint php reparaya-p1-web:latest -l app/Controllers/InicioController.php` y la captura que aportó muestra `No syntax errors detected in app/Controllers/InicioController.php`.
- Erick ejecutó también `docker run --rm -v "${PWD}:/lab" -w /lab --entrypoint php reparaya-p1-web:latest -l app/Views/inicio.php`: `No syntax errors detected in app/Views/inicio.php` (salida íntegra que pegó en la conversación).
- Estado: **tres pruebas de sintaxis superadas personalmente por Erick**. Evidencia de pantalla del controlador previamente revisada y salidas completas pegadas en la conversación. **No se ha guardado un PNG en esta carpeta local todavía**; recomendar una sola captura que reúna las tres salidas bajo `P2_Erick_03_PHP_sintaxis.png`.
- Captura completa posterior recibida en la conversación y verificada: aparecen `No syntax errors detected` de `public/index.php`, `app/Controllers/InicioController.php` y `app/Views/inicio.php`. Se preparó copia íntegra descargable bajo el nombre `P2_Erick_03_PHP_sintaxis.png` (SHA-256 `a04532de9627b3589f13c4bdded8a6d6f8801b0032d71da96c6fae14b6b20c46`). Copia en la carpeta local pendiente de que el estudiante la descargue; **no inventar que se copió al ordenador Windows**.
- No confundir `php -l` con pruebas funcionales HTTP, seguridad, reglas de negocio o conexión a la base de datos.

### Registro de avance 28/10/2026 — Erick — primera edición personal de PHP/MVC

- Captura de Visual Studio Code recibida y revisada, mostrando el árbol `app/Controllers`, `Views` y `public` del laboratorio `erick-lab-01`.
- **Edición efectiva guardada y comprobada en disco:** `app/Controllers/InicioController.php`, línea 10, `$mensaje = 'probando el php y mejorando mi conocimiento de php para reparar ya';`.
- El 28/10 se ejecutó `php -l app/Controllers/InicioController.php` con Docker tras iniciar Docker Desktop: **`No syntax errors detected` (código de salida 0)**. Sigue pendiente de prueba HTTP tras la modificación; **no afirmar todavía que se ve en el navegador**.
- La captura original está aportada al chat, pero todavía no se ha copiado un archivo PNG a `Doc_Captures_Print` en el ordenador. Recomendación de nombre: `P2_Erick_02_Estructura_MVC_VSCode.png`.
- El error `failed to connect to docker API ... dockerDesktopLinuxEngine` de la terminal VS Code se diagnosticó como **Docker Desktop cerrado**, no un error del código PHP. Se inició y verificó el motor `29.7.2` en la misma sesión. Próximo paso: **Erick ejecuta** únicamente el contenedor `reparaya-erick-lab01` sobre `127.0.0.1:8082`, refresca el navegador, comprueba su mensaje y guarda evidencia. No publicar en Sites ni marcar CRUD como completado antes de la verificación funcional y documental.

### Registro de incidencia E06 — 28/10/2026 — Docker Desktop cerrado

- **Captura exacta del usuario** de VS Code (1269 × 971, PNG) recibida en la conversación el 28/10, con el código PHP personal de Erick y el fallo del comando `docker info --format '{{.ServerVersion}}'`.
- **Diagnóstico reproducible:** el sistema carecía de conexión al `dockerDesktopLinuxEngine` por no estar ejecutándose Docker Desktop. No hay indicio en esta captura de un error sintáctico de PHP.
- **Corrección comprobada con Chat On Steroids Core:** inició Docker Desktop desde su instalación local. `docker info --format '{{.ServerVersion}}'` pasó a devolver `29.7.2`, y la prueba `php -l app/Controllers/InicioController.php` devolvió `No syntax errors detected` (exit 0).
- **Sin afirmar aún**: la captura no demuestra que se haya servido correctamente la página ni que los CRUD de P2 estén terminados. Queda la prueba HTTP del mensaje personalizado en `127.0.0.1:8082`.
- **Integridad:** se preservó la imagen original como `P2_Erick_06_Incidencia_Docker_Desktop_cerrado.png` en los archivos descargables de esta conversación, SHA-256 `50cccbd85d31d79694c4104d8762eb4f82b659055726b299c9f909de39e97f73`. **La imagen aún no existe en esta carpeta Windows**, por lo que el estudiante debe descargarla aquí; conservar el original sin alterar. Comprobar y ocultar rutas personales al preparar la versión pública de Sites.
- Informe técnico asociado: `INCIDENCIA_E06_DOCKER_DESKTOP_2026-10-28.md` en esta misma carpeta; el informe sí está guardado en Windows. Documentar como problema de entorno y corrección propia, no como incidencia asignada INC-05 del Producto 1.

### Registro de diagnóstico E07 — 28/10/2026 — Ruta `/**` produce 404

- Erick aportó dos imágenes reales: navegador con dirección `http://127.0.0.1:8082/**` y texto `404 - Página no encontrada`, y terminal de VS Code mostrando `docker info` correcto y el contenedor `reparaya-erick-lab01` en estado `Up` con puerto `127.0.0.1:8082->8000/tcp`.
- **Reproducción técnica con Chat On Steroids Core, desde su PC:** `GET http://127.0.0.1:8082/` → **HTTP 200** y HTML con `ReparaYa — Laboratorio MVC` y la frase personal de Erick; `GET http://127.0.0.1:8082/**` → **HTTP 404** con texto `404 - Página no encontrada`; `GET /no-existe` → **HTTP 404**.
- Diagnóstico: el controlador `public/index.php` solo admite `$ruta === '/'`; cualquier otra ruta entra en el bloque `else` y devuelve 404 deliberadamente. No cambiar código para arreglarlo: usar la URL exacta sin asteriscos `http://127.0.0.1:8082/`.
- El resultado **HTTP 200 está verificado por la herramienta**. Posteriormente Erick aportó una captura del navegador con la ruta principal correcta y su texto; el punto quedó completado el 28/10.
- Las capturas originales existen en la conversación; **no se han materializado PNG en esta carpeta Windows**. Registrar el proceso y guardar el PNG cuando el usuario lo descargue o se consiga copia autorizada.

### Registro de evidencia E04 — 28/10/2026 — Primera ruta MVC funcionando

- **Captura original comprobada:** Chrome abierto en `127.0.0.1:8082` (ruta principal `/`), encabezado `ReparaYa — Laboratorio MVC`, texto personal `probando el php y mejorando mi conocimiento de php para reparar ya` y línea `Ruta / → InicioController → inicio.php`.
- **Comprobación adicional real por Core:** `GET http://127.0.0.1:8082/` devuelve `HTTP 200`; el HTML incluye el título y la frase de Erick. No es solo una salida esperada: fue ejecutada y verificada tras recibir la captura.
- **Aprendizaje demostrado:** cambio propio de variable `$mensaje` en un controlador PHP; recarga de vista que lo muestra; diferencia entre ruta válida `/` y ruta inexistente `/**`, que devuelve `404` de forma intencionada.
- **Copia de imagen descargable** de esta conversación: `P2_Erick_04_Pagina_inicio.png` (2048×1106, 124758 bytes, SHA-256 `0e4f85f0c77cc30c7b5398f3ea9a64fd4e2a54576f3c45f7a8e852614de3a196`). **Aún no hay copia PNG de esta imagen en la carpeta Windows del usuario**; debe descargar y guardar la imagen aquí sin modificar el original.
- **Límite académico:** se ha validado únicamente el flujo entrada → controlador → vista, no hay modelo ni autenticación, bases de datos o CRUD completo. No mover tarjetas a `Hecho` por esta captura.
- **Próximo hito**: aprender a crear una vista de formulario de registro, incorporarla al router con ruta `GET /registro` y después validar el `POST` del formulario en servidor; PostgreSQL/MySQL y creación de usuarios reales quedan pendientes del SQL oficial.

### Registro de evidencia E09 — 28/10/2026 — Vista de formulario creada

- Erick creó personalmente `app/Views/registro.php` y aportó captura de Visual Studio Code. El archivo real contiene tres campos `nombre`, `correo` (tipo email) y `clave` (tipo password), más `<form action="/registro" method="post">`.
- **Prueba ejecutada:** `php -l app/Views/registro.php` desde la imagen PHP del laboratorio devolvió `No syntax errors detected in app/Views/registro.php`.
- **HTTP actual:** `GET http://127.0.0.1:8082/registro` devuelve 404 porque el router aún solo admite `/`. No es un error de la vista.
- Se preservó copia íntegra descargable `P2_Erick_09_Vista_registro_codigo.png` en la conversación (PNG 1264×1163; SHA-256 `e4143c507ad14c06b82f260d4e093a849b3266b7c07e62de9fda736a71865964`). **No se ha copiado automáticamente al PC Windows**.
- Este formulario aún **no registra usuarios ni almacena contraseñas**, no hay validación del lado servidor ni SQL oficial integrado. Evitar contraseñas reales y no marcar el CRUD como terminado.
- Próximo paso UocFlow: Erick agrega el método de presentación en el controlador, luego la ruta GET `/registro` en el router; prueba HTTP 200. Después proceder a validación segura de POST.

## Antes de guardar o compartir

- Recortar contraseñas, tokens, claves, configuración `.env`, datos personales innecesarios y rutas privadas no pertinentes.
- Evitar pantallas con otros proyectos o terminales ajenas a ReparaYa.
- No modificar la captura para falsear salidas: si una prueba falla, conservarla como evidencia de diagnóstico y registrar cómo se corrigió.
- Separar siempre **práctica de aprendizaje** (laboratorio MVC) de **evidencia de un CRUD funcional** (registro, autenticación, PDO, solicitudes, historial); la primera ayuda al proceso, pero no demuestra la segunda.
- Google Sites permanece sin publicar; no transferir capturas a Sites, GitHub o Canvas hasta revisar su contenido y decidir dónde corresponde.

## Próximo hito

**Laboratorio 01 superado**: Erick editó `../erick-lab-01/app/Controllers/InicioController.php` y confirmó visualmente la frase en el navegador; HTTP 200 también verificado con Core. Ahora UocFlow guía el formulario de registro, empezando por la vista `../erick-lab-01/app/Views/registro.php` y avanzando paso a paso hacia la ruta `GET /registro`. No atribuir persistencia de usuarios sin SQL real.

### E10 — 28/10/2026 · Método nuevo y regresión detectada después de la captura

- La **captura original** de Erick muestra ambos métodos `mostrarRegistro()` y `mostrar()`, junto con la salida `No syntax errors detected in app/Controllers/InicioController.php`. El archivo exacto del original fue recuperado desde las capturas automáticas de Windows y guardado aquí con nombre `P2_Erick_10_Controlador_mostrarRegistro_PHP_OK.png` (SHA256 `f3d0bc4ff7fdbbf8e52243b1551b08da88909de8cba07cefd7f8f0900b0da694`).
- **Cambio posterior en el archivo real:** al revisar a las 17:10, `InicioController.php` solo conservaba `mostrarRegistro()`; se había perdido el método `mostrar()` que usa la ruta `/` en `public/index.php`. `php -l` aún pasaba porque el archivo es sintácticamente válido, pero `GET /` contenía un error fatal: `Call to undefined method InicioController::mostrar()`. La herramienta HTTP devolvía código 200 con el mensaje fatal en su HTML por comportamiento de este servidor de desarrollo. **No interpretar HTTP 200 por sí solo como éxito; comprobar también el cuerpo y los registros.**
- **Acción formativa pendiente de Erick:** restaurar el método `mostrar()` dentro de la misma clase sin borrar `mostrarRegistro()`. Volver a ejecutar `php -l` y verificar que el HTML de la ruta `/` contiene el título y la frase personalizada. Luego añadir GET `/registro` al router.
- **Resuelto por Erick con Ctrl+Z:** el archivo del controlador vuelve a contener ambos métodos. Se volvió a comprobar con Chat On Steroids Core: `php -l` indica `No syntax errors detected`, `GET /` devuelve HTTP 200 con `ReparaYa — Laboratorio MVC` y el mensaje personal, sin texto `Fatal error`. Siguiente paso: registrar `GET /registro` en `public/index.php`.
- El proyecto es todavía un ejercicio separado de P2, sin CRUD 1 definitivo ni datos reales. No marcar la tarjeta Trello `Hecho`.

### E11 — 28/10/2026 · Ruta GET `/registro` visible en el navegador

- Erick modificó el router `public/index.php` por sí mismo; ahora distingue los métodos HTTP de `/` y `/registro` y rechaza el procesamiento de un registro aún no implementado.
- Captura de navegador recibida en el chat con `127.0.0.1:8082/registro` y formulario visible. El archivo **original** de Windows `Captura de pantalla 2026-10-28 171506.png` fue copiado en esta carpeta como `P2_Erick_11_Formulario_registro_GET_200.png`. Coincidencia SHA-256 comprobada y también inspección visual del PNG guardado.
- Verificación real con Chat On Steroids Core: `GET /` → HTTP 200; `GET /registro` → HTTP 200 y texto «Crear cuenta en ReparaYa»; `GET /no-existe` → HTTP 404. Los tres archivos `public/index.php`, `app/Controllers/InicioController.php` y `app/Views/registro.php` pasaron `php -l`.
- Prueba de seguridad/alcance **sin enviar ningún dato**: `POST /registro` → HTTP **405**, `Allow: GET`, cuerpo `El envío del registro todavía no está disponible`. Se explica que la página es una **vista funcional, no un registro funcional** y no guarda contraseñas.
- Siguiente tarea de Erick: construir validación de servidor para nombre/correo/contraseña de **datos ficticios**, sin DB, sin imprimir ni persistir la clave; después enlazar `POST /registro` y probar casos válidos e inválidos. Mantener la prohibición de Laravel del P2 y esperar el SQL oficial para el modelo.
