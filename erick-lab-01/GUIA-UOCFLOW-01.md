# UocFlow · Primera práctica de Erick · FP.448 P2

Estado: **laboratorio formativo**, no equivale a CRUD ni a una implementación definitiva del Producto 2. No compartirlo como «P2 terminado» ni publicarlo en AWS.

## Antes de comenzar (lectura mínima)

- Enunciado P2: https://aula.uoc.edu/courses/91603/assignments/989195. Leer «Restricción Técnica Estricta», «Crear la aplicación web» y lista de cuatro CRUD, centrado en **CRUD Usuarios**.
- Material oficial UOC: «MVC» http://materials.cv.uoc.edu/cdocent/PID_00283715/ . El recurso es un vídeo; no hay paginación PDF verificada. Si la web no abre, el ejercicio de abajo basta para practicar el concepto por ahora.
- Documento de Fase 3 almacenado en `../../Recursos-adicionales/[FASE3] FP448_FASE3.pdf`: **páginas 2 y 4** para la memoria de Google Sites, no para sintaxis PHP.
- Referencia PHP necesaria: https://www.php.net/manual/es/function.require-once.php y https://www.php.net/manual/es/function.htmlspecialchars.php . Leer solo «Descripción» y el primer ejemplo de cada una.

## Qué aprenderás

1. La **entrada** `public/index.php` recibe la URL y decide qué controlador se ejecuta.
2. El **controlador** `app/Controllers/InicioController.php` prepara `$titulo` y `$mensaje`.
3. La **vista** `app/Views/inicio.php` genera HTML con esos datos; `htmlspecialchars` evita interpretar texto como HTML.
4. Todavía no hay **modelo**: se incorporará al tener el SQL inicial auténtico del profesor. No inventar tablas.

## Prueba en tu ordenador (PowerShell)

```powershell
Set-Location 'C:\Users\guill\Documents\UOC\FP448-Backend-PHP-MVC\Producto-2\erick-lab-01'
docker info --format '{{.ServerVersion}}'
docker run --rm -v "${PWD}:/lab" -w /lab --entrypoint php reparaya-p1-web:latest -l public/index.php
docker run --rm -v "${PWD}:/lab" -w /lab --entrypoint php reparaya-p1-web:latest -l app/Controllers/InicioController.php
docker run --rm -v "${PWD}:/lab" -w /lab --entrypoint php reparaya-p1-web:latest -l app/Views/inicio.php
```

Resultado esperado para cada archivo: `No syntax errors detected`. Si el motor Docker está apagado, abrir Docker Desktop e intentarlo de nuevo.

Para verla en el navegador, iniciamos un servidor HTTP local, únicamente en `127.0.0.1:8082`:

```powershell
docker run --rm -d --name reparaya-erick-lab01 -p 127.0.0.1:8082:8000 -v "${PWD}:/lab" -w /lab --entrypoint php reparaya-p1-web:latest -S 0.0.0.0:8000 -t public
```

Abre http://127.0.0.1:8082/ y comprueba que aparece `ReparaYa — Laboratorio MVC`.

## Tu ejercicio manual (3 minutos)

1. Abre el archivo `app/Controllers/InicioController.php` con VS Code.
2. Cambia **solo** el contenido de `$mensaje` por un texto redactado por ti, por ejemplo `He entendido cómo el controlador entrega datos a la vista`.
3. Guarda el archivo, recarga http://127.0.0.1:8082/ y comprueba que cambia el párrafo.
4. Abre http://127.0.0.1:8082/no-existe . Esperado: respuesta HTTP 404 y el mensaje `Página no encontrada`.

Guarda una captura de la página y otra del 404 **solo si ambas se han probado realmente**. No incluyas credenciales, IP privadas ni identificadores personales innecesarios.

## Finalizar el servidor cuando acabes

```powershell
docker stop reparaya-erick-lab01
```

## Próxima misión, todavía NO implementada

Incorporar un formulario de registro con **validación en servidor** y después integrar la tabla de usuarios del SQL oficial. `password_hash`, `password_verify`, sesiones y consultas PDO preparadas solo se conectarán a una base real y acordada con Paula; no se guardarán contraseñas en texto plano.

Evidencias / memoria: anotar lo aprendido y capturas de **laboratorio**, no presentarlo como funcionalidad terminada de P2. Tarjeta de referencia: https://trello.com/c/bAyUGeLV .
