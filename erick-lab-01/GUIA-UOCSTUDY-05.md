# UocStudy 05 — ReparaYa P2: Docker, PDO y usuarios reales

**Situación comprobada (06/11/2026):** el primer laboratorio de Erick ya tiene modelo de usuario y base MySQL propia. El asistente implementó la demostración por petición de Erick. Sigue siendo un proyecto de desarrollo local, **no la entrega final integrada**. Fecha límite P2 en Canvas: domingo **08/11/2026 a las 23:59 (Madrid)**.

## 1. Lo que hay en tu VS Code

| Archivo | Qué significa |
| --- | --- |
| `../entorno-p2/compose.yaml` | Arranca juntos PHP y MySQL en una red y volumen P2 exclusivos. |
| `../entorno-p2/.env` | Contraseñas de desarrollo que generó un script; nunca se comparten ni se suben a Git. |
| `../SQL-oficial-original/bbddReparaYa(1).sql` | Tabla `usuarios` y otras tres tablas del profesor, SIN MODIFICAR. |
| `public/index.php` | Router: selecciona controlador según URL y verbo GET/POST; arranca sesión. |
| `app/Controllers/InicioController.php` | Conserva tus métodos `mostrar()` y `mostrarRegistro()`. |
| `app/Controllers/UsuarioController.php` | Valida formularios, llama al modelo y decide respuestas 303/401/403/409/422. |
| `app/Models/Usuario.php` | SQL con PDO y `prepare()/execute()`; altas y modificaciones solo por identificador. |
| `app/Database/Conexion.php` | Único lugar donde se crea la conexión PDO leyendo variables `DB_*`. |
| `app/Views/registro.php`, `login.php`, `perfil.php` | HTML mostrado al usuario, con campos escapados por `htmlspecialchars`. |
| `../entorno-p2/probar-flujo-p2.ps1` | Prueba 22 casos reales del ciclo de cuenta ficticia. |

## 2. Lectura mínima (no estudiar manuales enteros)

- Enunciado oficial P2: https://aula.uoc.edu/courses/91603/assignments/989195. Leer solo «Importar la base de datos», «Registro-Login», «CRUD Usuarios», «Defensa técnica individual» y las líneas sobre uso de IA.
- Material oficial UOC «Conexión a BBDD con PHP»: http://materials.cv.uoc.edu/cdocent/PID_00283713/. **Es vídeo**; paginación no aplicable. Priorizar la explicación de conexión PDO/MySQL; no se ha verificado un minuto concreto.
- PHP: https://www.php.net/manual/es/book.pdo.php — apartados `PDO::__construct`, `PDO::prepare` y `PDOStatement::execute`.
- PHP: https://www.php.net/manual/es/function.password-hash.php y https://www.php.net/manual/es/function.password-verify.php — leer descripción y ejemplo básico.
- PHP: https://www.php.net/manual/es/function.session-start.php — descripción y uso inicial.

## 3. Estudiar por bloques de 15–25 minutos

### Bloque A — Docker y base de datos

Desde PowerShell:

```powershell
Set-Location 'C:\Users\guill\Documents\UOC\FP448-Backend-PHP-MVC\Producto-2\entorno-p2'
docker compose ps
docker compose exec -T web php -r 'require "app/Database/Conexion.php"; echo Conexion::abrir()->query("SELECT 1")->fetchColumn() . PHP_EOL;'
```

- `docker compose ps`: muestra **solo** servicios del proyecto P2 actual.
- `exec -T web php -r`: ejecuta una instrucción corta de PHP dentro del contenedor `web`.
- `require`: carga el archivo centralizado de conexión; `SELECT 1` prueba que MySQL responde.
- Esperado: `1`. Si no responde, **no toques P1**; comprueba primero `docker compose ps`.
- Evidencia: resultado `PDO_SELECT=1`, cuatro tablas y ambos contenedores P2 activos.

**Pregúntate:** ¿por qué la web usa `DB_HOST=db`, no `localhost`? Porque `db` es el nombre del servicio de MySQL dentro de la red Docker.

### Bloque B — Modelo y consulta preparada

En VS Code abre `app/Models/Usuario.php`, método `buscarPorCorreo()`. Identifica `SELECT ... WHERE email = :correo`, `prepare()` y `execute(['correo' => $correo])`.

- `:correo` es un **marcador**, no texto SQL concatenado a partir de un formulario.
- El controlador valida; el modelo consulta; la vista muestra.
- Ejercicio autónomo: explica con una frase dónde cambiarías la consulta si quisieras buscar usuarios por `id` (¡hay un ejemplo justo debajo!). No modifiques aún la consulta que pasa las pruebas.
- Evidencia académica: captura de modelo más prueba de registro sin mostrar hash ni contraseñas.

### Bloque C — Registro, hash y sesión

Abre `UsuarioController.php` y localiza las líneas `password_hash()`, `password_verify()`, `session_regenerate_id(true)` y `$_SESSION['usuario_id']`.

- `password_hash()` convierte la contraseña en un hash no reversible que guarda MySQL.
- `password_verify()` compara una contraseña escrita con el hash guardado.
- `$_SESSION['usuario_id']` recuerda qué usuario ha iniciado sesión.
- `session_regenerate_id(true)` cambia el identificador de sesión tras entrar para reducir ataques de fijación de sesión.
- El token `_csrf` comprueba que el POST procede del formulario mostrado en esa sesión.

En el navegador abre `http://127.0.0.1:8083/registro` y usa **solo datos inventados**. Comprueba la redirección a `/login`, entra y abre `/perfil`. No uses tu correo real ni una contraseña que utilices de verdad.

### Bloque D — Repite las pruebas

```powershell
.\probar-flujo-p2.ps1
```

Esperado: `TOTAL_PRUEBAS=22/22` y `REGISTRO_CIFRADO_Y_ROL=PASS`. El script deja un nuevo usuario **ficticio** en la BD para comprobar persistencia. Si falla, mira primero el nombre del caso con `FAIL` y analizamos juntos la causa.

## 4. Lo que debes poder explicar en una defensa de 2 minutos

1. El navegador envía `POST /registro` al router de `public/index.php`.
2. `UsuarioController` valida datos y ejecuta `password_hash()`.
3. `Usuario::crearParticular()` lanza un `INSERT` preparado mediante PDO.
4. MySQL guarda nombre, email, hash y rol `particular` en la tabla oficial `usuarios`.
5. Una prueba con correo duplicado devuelve 409; una contraseña incorrecta devuelve 401.

**Prueba corta para ti:** ¿qué cambiaría si guardáramos la contraseña directamente en SQL en lugar de usar `password_hash()`? ¿Por qué una consulta `prepare()` es preferible a pegar el correo del usuario dentro de una cadena SQL?

## 5. Documentación y rúbrica

- Evidencia técnica exacta: `../PRUEBA-PDO-USUARIOS-2026-11-06.md`.
- Capturas antiguas auténticas: `../Doc_Captures_Print/LEEME_EVIDENCIAS_P2.md`; **las nuevas todavía están pendientes**.
- Site: cuando Erick y Paula acuerden la estructura de P2, incluir un bloque compacto «Conexión PDO + registro/login/perfil», capturas sin secretos, dificultad del test de redirecciones y su corrección. No publicarlo automáticamente.
- Trello: tarjetas CRUD1 y BD/MVC continúan pendientes de la revisión grupal; no marcarlas `Hecho` solo por 22 pruebas locales.
- Rúbrica relacionada: «Base de datos» (2 puntos) y «Registro-Login y Perfil» (3 puntos), aún **parciales** hasta integrar roles, documentación y revisión.
- Dependencia de Paula: conseguir su código o repositorio y acordar migración SQL adicional de incidencias, antes de hacer un calendario o tabla incompatibles.

## 6. Próximo paso útil después de esta guía

Integrar la contribución de Paula si existe; crear una ampliación SQL compatible para el estado `En curso`, historial, precio base, franja/telefono y localizador; desarrollar el primer caso completo de **solicitud** con validación de 48 horas e historial. Paralelamente iniciar Git compartido y reservar tiempo real para Word, mapa manuscrito, vídeo, AWS y entrega. No se ha enviado ninguna actividad.
