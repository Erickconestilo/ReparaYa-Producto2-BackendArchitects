# UocFlow 03 — conectar la vista de registro al controlador y a la ruta

**28/10/2026 — Erick — Producto 2, práctica formativa de PHP nativo.**

## Situación comprobada

- Erick creó `app/Views/registro.php` y compartió captura en el chat; comprobación PHP de sintaxis correcta.
- `GET /` funciona (HTTP 200).
- `GET /registro` devuelve 404 porque `public/index.php` todavía no tiene esa ruta. **No cambiar el HTML por este motivo**.
- El formulario todavía no guarda usuarios ni valida contraseñas; no usar cuentas ni contraseñas reales.

## Paso A — Erick modifica el controlador (solo este paso primero)

En `app/Controllers/InicioController.php`, añadir el siguiente método **dentro de `final class InicioController`, después de `mostrar()` y antes de la última llave**:

```php
    public function mostrarRegistro(): void
    {
        require dirname(__DIR__) . '/Views/registro.php';
    }
```

Guardar con Ctrl+S y comprobar su sintaxis desde la terminal en `erick-lab-01`:

```powershell
docker run --rm -v "${PWD}:/lab" -w /lab --entrypoint php reparaya-p1-web:latest -l app/Controllers/InicioController.php
```

Esperado: `No syntax errors detected in app/Controllers/InicioController.php`. El método nuevo permite **mostrar** la vista; aún no crea una ruta ni registra datos. Si aparece un error, revisar la posición de las llaves `}`.

## Paso B — cuando el método esté probado, conectar GET /registro

Abrir `public/index.php`. Mantener `require_once`, `$ruta` y el bloque existente de inicio. Añadir una comprobación del método HTTP y reemplazar solo la lógica de rutas por:

```php
$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($ruta === '/' && $metodo === 'GET') {
    (new InicioController())->mostrar();
} elseif ($ruta === '/registro' && $metodo === 'GET') {
    (new InicioController())->mostrarRegistro();
} elseif ($ruta === '/registro') {
    http_response_code(405);
    header('Allow: GET');
    echo 'Registro todavía no disponible';
} else {
    http_response_code(404);
    echo '404 - Página no encontrada';
}
```

Pasar `php -l public/index.php`, después abrir `http://127.0.0.1:8082/registro` y verificar que muestra el formulario (HTTP 200). Confirmar que `http://127.0.0.1:8082/` sigue funcionando y que una ruta inexistente devuelve 404.

**No enviar el formulario todavía**: su procesamiento POST falta a propósito, y el `405` evita que una contraseña de prueba termine tratada como registro válido. La validación del servidor, sesiones, consultas SQL preparadas y `password_hash/password_verify` se implementarán cuando se acuerde la base oficial con Paula.

## Evidencia

Guardar imagen de VS Code con el método nuevo y luego una captura de formulario visible en el navegador, con URL legible y sin contraseñas. Carpeta `../Doc_Captures_Print`. No marcar CRUD1 como Hecho con esta práctica.
