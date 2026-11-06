# Evidencia E06 · Diagnóstico de Docker Desktop cerrado (Producto 2, laboratorio MVC)

**Fecha:** 28/10/2026 · **Estudiante:** Erick · **Proyecto:** ReparaYa, FP.448, Grupo 5 BackendArchitects.

> Esta es una incidencia **del laboratorio de aprendizaje de P2**, no la incidencia oficial INC-05 del Producto 1. No utilizar como prueba de un CRUD funcional.

## 1. Operación que intentaba realizar

Después de modificar personalmente la variable `$mensaje` del controlador `app/Controllers/InicioController.php`, se intentó comprobar la disponibilidad del motor con:

```powershell
docker info --format '{{.ServerVersion}}'
```

## 2. Resultado observado

La terminal integrada de VS Code devolvió un error que indicaba falta de conexión con la API de Docker en `npipe:////./pipe/dockerDesktopLinuxEngine` y que no encontraba el archivo del conducto. **La imagen original se aportó al chat y se prepara para guardar con el nombre `P2_Erick_06_Incidencia_Docker_Desktop_cerrado.png`.**

## 3. Diagnóstico

Se comprobó que **Docker Desktop no estaba iniciado**. El problema afectaba a la disponibilidad del motor y no demostraba un fallo del código PHP. El cliente `docker` puede estar instalado, pero requiere que el motor esté funcionando para iniciar contenedores.

## 4. Corrección y verificación ya realizadas

Con apoyo de Chat On Steroids Core se inició la instalación local de Docker Desktop. Tras el arranque:

```text
docker info --format '{{.ServerVersion}}' => 29.7.2
php -l app/Controllers/InicioController.php => No syntax errors detected
```

El segundo comando se ejecutó dentro de la imagen de PHP ya existente `reparaya-p1-web:latest`; la prueba devolvió código de salida 0. Los dos resultados fueron **verificados con herramientas**, no capturados todavía conjuntamente en la carpeta de evidencias.

## 5. Próxima comprobación

Arrancar únicamente el contenedor `reparaya-erick-lab01` del laboratorio, abrir `http://127.0.0.1:8082/` y comprobar que se representa el texto editado por Erick. Guardar evidencia visual de la respuesta de navegador antes de dar la práctica por cerrada.

## 6. Aprendizaje y uso en la memoria

La diferencia entre **cliente Docker instalado**, **Docker Engine activo** y **servidor PHP dentro de un contenedor** es importante: un error de conexión al motor debe diagnosticarse antes de modificar el código de la aplicación. Este registro sirve como ejemplo de comprobación y corrección propia del entorno. No debe confundirse con la aplicación final de gestión de usuarios o incidencias.

**Archivo de la imagen:** `P2_Erick_06_Incidencia_Docker_Desktop_cerrado.png` (pendiente de descargar a esta carpeta desde la conversación).
