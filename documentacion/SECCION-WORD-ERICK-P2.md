# Texto base para el Word técnico · Aportación de Erick

> **Borrador académico para revisión personal.** Erick debe leer, corregir con sus palabras y comprobar las evidencias antes de incorporar esta parte al documento del grupo. No afirma que el Producto 2 entero esté terminado.

## 1. Arquitectura de la aplicación

Para ReparaYa hemos organizado el código en PHP nativo siguiendo el patrón MVC. El punto de entrada `public/index.php` decide qué controlador debe intervenir según la ruta y el método de la petición. `UsuarioController` se ocupa del registro, inicio de sesión y gestión del perfil. `SolicitudController` controla la creación, consulta, edición y cancelación de solicitudes de reparación.

Los modelos `Usuario.php` y `Solicitud.php` contienen el acceso a MySQL mediante PDO y sentencias preparadas. Las vistas son archivos PHP que presentan los formularios y resultados y escapan el contenido mostrado. Esta separación nos permite localizar mejor dónde se valida cada regla y dónde se guarda realmente la información.

**Imagen sugerida:** exportación PNG de `mvc-reparaya.html`, con el pie «Arquitectura MVC implementada en el repositorio, antes de integrar los módulos pendientes del equipo».

## 2. Reglas de negocio que funcionan en la parte del cliente

Una solicitud estándar debe programarse con un mínimo de 48 horas de antelación. Las urgentes pueden solicitarse con menor antelación si la hora de la cita sigue siendo futura. El cliente consulta únicamente sus propias incidencias, mediante consultas que filtran también por su identificador de sesión. La modificación de datos se limita a avisos pendientes con suficiente antelación. Las cancelaciones de cliente solo se aceptan para avisos pendientes o asignados si faltan más de 48 horas para la cita.

Al crear una incidencia, la aplicación almacena el aviso y su primer asiento de historial dentro de una transacción. Cuando el cliente cancela un aviso, otra transacción actualiza su estado y conserva el registro del cambio. Para la cancelación se utiliza `SELECT ... FOR UPDATE`, que ayuda a controlar operaciones concurrentes sobre la misma fila.

**Imagen sugerida:** exportación PNG de `estados-incidencia.html`, con el pie «Ciclo exigido por el enunciado; asignación y acciones del técnico aún pendientes de integrar».

## 3. Seguridad y validaciones

Las contraseñas no se guardan en texto claro: usamos `password_hash` y `password_verify`. Los formularios POST utilizan tokens CSRF, los accesos privados requieren una sesión válida y las consultas preparadas separan parámetros y SQL. También verificamos errores de entrada y evitamos mostrar detalles internos de las excepciones al usuario final.

## 4. Evidencias técnicas

Las pruebas automatizadas sobre una instancia local de MySQL permiten verificar registro, login, perfil, solicitudes, reglas de 48 horas, restricciones de acceso e historial. Erick ejecutó desde PowerShell 5.1 las pruebas de usuarios (22/22) y solicitudes (20/20) el 06/11. Hay un informe adicional de 26 pruebas de edición ejecutadas inicialmente por el asistente; su resultado debe revisarse por Erick antes de declararlo como verificación propia.

**Capturas pendientes de seleccionar:** consola de pruebas, navegador mostrando una solicitud ficticia, consulta de historial, estructura de Git y prueba final de AWS. Evitar contraseñas, tokens y datos personales.

## 5. Pendientes que no se deben presentar como terminados

La administración, el calendario diario/semanal/mensual, la gestión de técnicos/servicios, el ciclo completo de estados, la integración del trabajo de Paula y la demostración en AWS necesitan verificación conjunta. Las evidencias del vídeo y de la defensa individual se añadirán cuando existan realmente.

## 6. Uso de IA y aprendizaje

La IA ayudó a desarrollar y revisar parte del código y de las pruebas. La declaración académica debe identificar **interacciones auténticas**, herramienta, problema, decisión y archivos o resultado comprobable. No inventar cuatro prompts para rellenar el requisito. Erick debe explicar qué comprendió y qué validó personalmente.
