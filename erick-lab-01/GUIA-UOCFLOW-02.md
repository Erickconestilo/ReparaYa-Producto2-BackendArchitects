# UocFlow · Misión 02 · Erick · Formulario de registro (vista)

**Situación:** 28/10/2026. La práctica 01 ya funciona (`GET /` HTTP 200, texto propio de Erick visible en el navegador y en captura E04). **Este ejercicio todavía NO crea usuarios ni conecta a una base de datos**.

**Enunciado prioritario:** https://aula.uoc.edu/courses/91603/assignments/989195, apartados `Restricción Técnica Estricta`, `Registro-Login`, `Panel de Cliente Particular` y `CRUD 1 Usuarios`. PHP **nativo**, sin Laravel en P2.

## Paso que hace Erick ahora

1. En VS Code, abrir `app/Views` (carpeta existente), y crear **manualmente** el archivo `registro.php` (sin modificar los otros archivos).
2. Escribir/pegar el siguiente HTML y observar que cada `<label>` describe un campo. Guardar `Ctrl+S`.

```html
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de clientes | ReparaYa</title>
</head>
<body>
    <main>
        <h1>Crear cuenta en ReparaYa</h1>
        <p>Práctica MVC: este formulario todavía no registra usuarios.</p>
        <form action="/registro" method="post">
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" type="text" required maxlength="100">

            <label for="correo">Correo electrónico</label>
            <input id="correo" name="correo" type="email" required>

            <label for="clave">Contraseña de prueba</label>
            <input id="clave" name="clave" type="password" required minlength="8">

            <button type="submit">Crear cuenta</button>
        </form>
    </main>
</body>
</html>
```

3. Explicar con palabras propias qué son `form`, `method="post"`, `name`, `label`, `required` y por qué el `type="password"` oculta visualmente el contenido pero **no lo almacena de forma segura por sí mismo**.
4. **No hacer clic en “Crear cuenta” todavía:** la ruta `POST /registro` no existe y no hay almacenamiento. No introducir una contraseña real. Los atributos `required`, `type="email"` y `minlength` **no sustituyen la validación PHP en el servidor**.
5. Mostrar en una captura el archivo guardado y sus campos. Dejar evidencia en `../Doc_Captures_Print` bajo nombre provisional `P2_Erick_09_Vista_registro_codigo.png` una vez exista.

## Comprobación del archivo PHP

En la terminal de VS Code abierta en la carpeta `erick-lab-01`:

```powershell
docker run --rm -v "${PWD}:/lab" -w /lab --entrypoint php reparaya-p1-web:latest -l app/Views/registro.php
```

Esperado: `No syntax errors detected in app/Views/registro.php`. Esta prueba por sí sola no valida todo el HTML ni el registro.

## Próximo paso guiado (NO realizado todavía)

Con la vista guardada y revisada, añadir la ruta **GET `/registro`** en `public/index.php` y el método para cargar la vista en un controlador. Verificar `HTTP 200` en el navegador. Después crear validación segura del `POST`, conexión a la base SQL oficial del consultor, `password_hash`, consulta preparada y sesión. Mantener coordinación del esquema y permisos con Paula.

## Estado de evidencias

- E04: primera ruta MVC funcionando — **captura en chat y HTTP verificado**; la copia PNG al PC sigue pendiente.
- E06/E07: incidencias del entorno/ruta resueltas.
- La nueva vista es **una práctica de aprendizaje**, no evidencia de que CRUD 1 esté entregado.
