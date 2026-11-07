# Defensa técnica de Erick · ReparaYa P2

**Objetivo:** preparar una explicación individual de **máximo 2 minutos** sobre el código real. La rúbrica pide: enseñar un fragmento de su CRUD, relacionarlo con MVC, justificar las consultas preparadas y explicar una decisión técnica o un error resuelto.

**Importante:** este guion es para **estudiarlo y adaptarlo a tu voz**, no para leerlo sin comprenderlo. El CRUD de incidencias es la propuesta para tu defensa; confirma el reparto definitivo con Paula y el profesor. El desarrollo tuvo apoyo de IA, que se debe declarar de forma veraz donde exija la actividad.

## Guion natural para practicar (objetivo 1:40–1:55, según ritmo)

> Hola, soy Erick Chacón, de BackendArchitects. Voy a explicar cómo funciona la gestión de incidencias de ReparaYa.
>
> Cuando un cliente envía una solicitud, la petición entra por `public/index.php`, que identifica la ruta y llama a `SolicitudController`. Este controlador comprueba la sesión, valida los datos y aplica la regla de las 48 horas a las citas estándar.
>
> Después llama al modelo `Solicitud.php`. Ahí usamos PDO con consultas preparadas: el SQL lleva marcadores y los datos del formulario se pasan como parámetros, sin concatenarlos.
>
> Al crear un aviso guardamos también su primer historial dentro de una transacción. Si falla alguna operación, hacemos `rollBack` y evitamos datos incompletos.
>
> Las vistas, como `solicitar.php` y `detalle-aviso.php`, muestran los formularios y el historial. Usamos `htmlspecialchars` al presentar textos introducidos por personas.
>
> Una decisión técnica importante es la cancelación: el sistema comprueba que el cliente sea el propietario, que el estado lo permita y que falten más de 48 horas. Utilizamos `FOR UPDATE` para bloquear temporalmente la fila y evitar cambios simultáneos.
>
> Finalmente, verificamos reglas de negocio, acceso privado e historial mediante pruebas contra MySQL. Esto demuestra que las reglas se comprueban en el servidor, no solo en la interfaz. Gracias.

Si al ensayarlo tardas más de 1:55, recorta ejemplos: **no aceleres hasta hablar ininteligible**. No afirmes que ya están implementadas la administración, los técnicos o el calendario.

## Qué mostrar en VS Code, en el mismo orden

| Tiempo orientativo | Archivo y rango | Qué señalar |
|---|---|---|
| 0:00–0:15 | `erick-lab-01/public/index.php`, líneas 51–70 | Rutas `GET/POST` de crear, ver, editar y cancelar una incidencia. |
| 0:15–0:35 | `erick-lab-01/app/Controllers/SolicitudController.php`, líneas 50–87 | Identificación de cliente, validación de campos y regla de 48 horas. |
| 0:35–1:05 | `erick-lab-01/app/Models/Solicitud.php`, líneas 22–53 | `prepare`, `execute`, transacción, `INSERT` e historial inicial. |
| 1:05–1:20 | `erick-lab-01/app/Views/detalle-aviso.php`, líneas 5–24 | Presentación HTML y `htmlspecialchars`. |
| 1:20–1:45 | `erick-lab-01/app/Models/Solicitud.php`, líneas 150–186 | `FOR UPDATE`, estados permitidos, plazo, cambio de estado e historial. |
| 1:45–1:55 | Archivo de pruebas o informe de resultados | Terminar con lo que se verificó y un aprendizaje. |

**Plan B si te equivocas durante la grabación:** sigue explicando la idea, corta la grabación si es necesario y vuelve a grabar. Un vídeo natural de 1:50 vale más que una lectura rápida y confusa.

## Estudio: entender cada pieza sin tecnicismos vacíos

- **Ruta:** decide qué operación se ejecuta según la dirección (`/solicitar`, `/aviso/editar`) y el método HTTP (`GET` o `POST`).
- **Controlador:** recibe la petición y aplica las reglas. Comprueba rol, propiedad, datos y plazos.
- **Modelo:** consulta o cambia los datos con SQL y PDO.
- **Vista:** enseña formularios o resultados al usuario. No debe ejecutar consultas SQL.
- **PDO y `prepare`:** las consultas tienen marcadores como `:id` y los valores se pasan por separado a `execute`.
- **Transacción:** agrupa varias operaciones que deben completarse juntas o deshacerse juntas (`commit` / `rollBack`).
- **`FOR UPDATE`:** bloquea la fila consultada durante la transacción para evitar decisiones simultáneas con datos desactualizados.
- **CSRF:** compara un token del formulario para evitar operaciones POST enviadas desde otra página sin autorización del usuario.
- **`htmlspecialchars`:** convierte caracteres especiales en texto seguro para HTML cuando muestra contenido introducido por personas.
- **48 horas:** un aviso estándar debe crearse con 48 h como mínimo; un cliente solo puede cancelar una incidencia pendiente o asignada si faltan **más de** 48 h. Son reglas distintas.

## Miniexamen: ocho preguntas que debes saber contestar

1. **¿Qué significa MVC?** Modelo (datos), Vista (presentación) y Controlador (reglas y coordinación).
2. **¿Dónde está el SQL?** En los modelos, por ejemplo `app/Models/Solicitud.php`, no en los formularios.
3. **¿Por qué PDO preparado?** Separa la instrucción SQL de los parámetros; ayuda a prevenir inyección SQL por datos de formulario.
4. **¿Por qué una transacción al crear?** Porque incidencia e historial inicial deben guardarse juntas o no guardarse.
5. **¿Qué pasa si alguien cambia el `id` de la incidencia en la URL?** La consulta filtra por el ID de la incidencia y por el `cliente_id` de la sesión; si no pertenece a ese usuario, devuelve 404.
6. **¿Por qué comprobar las 48 h en PHP y no solo en el formulario?** Porque el navegador se puede modificar y el servidor es quien debe imponer la regla.
7. **¿Qué evita `FOR UPDATE`?** Que dos operaciones concurrentes decidan sobre la misma fila sin esperar la una a la otra.
8. **¿Qué está pendiente?** Administración, asignación de técnicos, calendario y pruebas integradas del ciclo hasta Finalizada; corresponden al trabajo pendiente del equipo.

## Práctica frente a la cámara

1. Abre VS Code en `Producto-2` y deja preparados esos cinco fragmentos.
2. Haz un ensayo sin grabarte, con cronómetro: intenta explicar cada archivo **mirando el código**, no recitando.
3. Haz un segundo ensayo grabado. Comprueba que la letra se vea, no aparezcan contraseñas ni datos reales y el audio sea claro.
4. Duración final **≤2:00**. Guarda el vídeo con un nombre identificable, por ejemplo `Defensa_Erick_Chacon_P2.mp4`.
5. Asegúrate de poder responder el miniexamen antes de considerarlo listo para entregar.

## Pruebas documentadas y autoría real

- 22/22 pruebas de registro, login y perfil: ejecutadas por Erick en PowerShell 5.1 el 06/11.
- 20/20 pruebas de solicitudes: ejecutadas por Erick en PowerShell 5.1 el 06/11.
- 26/26 pruebas de edición: ejecutadas inicialmente por el asistente; existe informe, pero **Erick debe revisarlas** antes de atribuirse su ejecución.
- La aplicación fue construida con ayuda de IA; no ocultar ese hecho ni atribuirse manualmente trabajo no realizado. La defensa consiste en **demostrar comprensión verdadera** del resultado.

## Diferencia entre este documento y la entrega

Este archivo sirve para ensayar. **No es el vídeo ni el Word entregado en Canvas.** Tampoco reemplaza el mapa conceptual obligatorio hecho a mano. La entrega oficial vence el **domingo 8 de noviembre de 2026 a las 23:59 (Europe/Madrid)**.
