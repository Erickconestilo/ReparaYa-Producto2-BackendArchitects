# UocFlow 04 — Recibir y validar el formulario en PHP nativo

**Proyecto:** ReparaYa · FP.448 · Producto 2 · **Erick** · 28/10/2026.

**Situación verificada:** `GET /registro` funciona y muestra nombre, correo y contraseña. `POST /registro` devuelve 405 a propósito: todavía no se aceptan registros. **No existe base de datos integrada y aquí NO se crean usuarios reales.**

## Lectura mínima (5–10 minutos)

- Enunciado del profesor: https://aula.uoc.edu/courses/91603/assignments/989195 — leer solo requisitos `Registro-Login` y `CRUD Usuarios`, incluido almacenamiento seguro de contraseñas y validación.
- Manual PHP `$_POST`: https://www.php.net/manual/es/reserved.variables.post.php — leer descripción y primer ejemplo.
- Manual PHP `filter_var`: https://www.php.net/manual/es/function.filter-var.php — leer descripción y el apartado de validación de correos si está disponible.
- Manual PHP `trim`: https://www.php.net/manual/es/function.trim.php — leer descripción.
- No leer todavía las secciones completas de PDO o sesiones: las abordaremos con el SQL oficial.

## Antes de editar: hay respaldo

Se guardó una copia exacta y verificada por SHA-256 de los archivos que funcionaban en `respaldo/InicioController_antes_validacion_2026-10-28.php.bak` y `respaldo/index_antes_validacion_2026-10-28.php.bak`. Los archivos con `.bak` no forman parte de la aplicación. **No borrar los métodos `mostrar()` y `mostrarRegistro()` existentes**.

## PASO 1 — Comprender la protección actual (no usar datos reales)

Con el contenedor `reparaya-erick-lab01` encendido, en la terminal de VS Code situada en `erick-lab-01`:

```powershell
curl.exe -i -X POST http://127.0.0.1:8082/registro
```

Resultado **ya comprobado por Chat On Steroids Core**: `HTTP/1.1 405 Method Not Allowed`, cabecera `Allow: GET` y cuerpo `El envío del registro todavía no está disponible`. El estado 405 indica que el servidor **conoce la ruta, pero todavía no admite POST**.

## PASO 2 — Añadir solo validación del lado servidor

Abrir `app/Controllers/InicioController.php`. **Sin sustituir ningún método anterior**, añadir al final de la clase, antes de la última llave `}`, este nuevo método:

```php
    public function procesarRegistro(): void
    {
        // Recibir los datos enviados por el formulario (POST).
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $correo = trim((string) ($_POST['correo'] ?? ''));
        $clave = (string) ($_POST['clave'] ?? '');

        $errores = [];

        if ($nombre === '' || strlen($nombre) > 100) {
            $errores[] = 'Escribe un nombre de hasta 100 caracteres.';
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Escribe un correo electrónico válido.';
        }
        if (strlen($clave) < 8) {
            $errores[] = 'La contraseña de prueba necesita al menos 8 caracteres.';
        }

        // Responder con información de prueba, SIN mostrar la contraseña ni guardar datos.
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: no-store');

        if ($errores !== []) {
            http_response_code(422);
            echo implode("\n", $errores);
            return;
        }

        echo 'Validación superada. No se ha creado ninguna cuenta.';
    }
```

**Qué entender:** `$_POST` es el conjunto de campos enviados; `trim` recorta espacios iniciales/finales; `filter_var` valida el formato del correo; `strlen` aquí mide bytes para una longitud mínima orientativa de clave de práctica. Un sistema real necesita una política más cuidadosa y `password_hash`; **no usar contraseñas verdaderas en este laboratorio**.

Guardar con `Ctrl+S` y ejecutar:

```powershell
docker run --rm -v "${PWD}:/lab" -w /lab --entrypoint php reparaya-p1-web:latest -l app/Controllers/InicioController.php
```

**No probar POST aún:** falta conectar el nuevo método a la ruta. Si aparece un error de sintaxis, comprobar las llaves `}` y que el método nuevo esté dentro de la clase, sin borrar `mostrar()` ni `mostrarRegistro()`.

## PASO 3 — Conectar POST sin perder GET

Cuando PASO 2 funcione, abrir `public/index.php`. Mantener la línea de `require_once` y la obtención de `$ruta` y `$metodo`. Reemplazar solamente:

```php
} elseif ($ruta === '/registro') {
    http_response_code(405);
    header('Allow: GET');
    echo 'El envío del registro todavía no está disponible';
```

por:

```php
} elseif ($ruta === '/registro' && $metodo === 'POST') {
    (new InicioController())->procesarRegistro();

} elseif ($ruta === '/registro') {
    http_response_code(405);
    header('Allow: GET, POST');
    echo 'Método no permitido';
```

Guardar. Comprobar sintaxis con `php -l public/index.php`, usando Docker como en el paso anterior. **No reemplazar todo `index.php`: se perderían las rutas ya verificadas.**

## PASO 4 — Pruebas con datos FICTICIOS (solo cuando pasos 2 y 3 estén correctos)

```powershell
# Datos ausentes: debe responder 422 con errores; no se crea ningún usuario.
curl.exe -i -X POST http://127.0.0.1:8082/registro

# Datos ficticios válidos: debe responder 200, aclarando que no se creó ninguna cuenta.
curl.exe -i -X POST http://127.0.0.1:8082/registro -d "nombre=Alumno+Prueba&correo=alumno%40example.test&clave=ClaveFicticia123"
```

Resultados esperados: para la primera prueba, `422 Unprocessable Content` (o nombre equivalente) y mensajes sin datos sensibles. Para la segunda, `200 OK` y `Validación superada. No se ha creado ninguna cuenta.`. El servidor **NO almacena ni devuelve la clave de prueba**, pero el comando de prueba queda en el historial de terminal: por eso usamos una clave inventada y nunca una real.

Volver a comprobar: `GET /` = página principal, `GET /registro` = formulario, ruta desconocida = 404. Guardar una captura de las pruebas sin datos reales ni contraseñas.

## Lo que todavía falta para P2

- Integración de la base **SQL oficial proporcionada por el profesor** (aún pendiente de conseguir).
- Modelo de usuario con PDO y consultas preparadas, hash seguro `password_hash()`, verificación `password_verify()`, sesiones, roles y perfiles.
- Integración con el trabajo de Paula, pruebas, Git, memoria y defensa. **No presentar este laboratorio como CRUD 1 terminado.**

Evidencia anterior: `../Doc_Captures_Print/P2_Erick_11_Formulario_registro_GET_200.png` (confirmada). Próximo registro solo después de ejecutar realmente PASO 1 o PASO 2.
