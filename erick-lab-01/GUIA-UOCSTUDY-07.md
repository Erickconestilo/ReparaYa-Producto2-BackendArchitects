# UocStudy 07 — Cómo se edita una solicitud en ReparaYa

Erick, esta práctica te enseña la **U de CRUD**: *Update* significa actualizar un registro
que ya existe. La nueva funcionalidad fue desarrollada por el asistente por tu autorización
para avanzar en P2; conviene que la compruebes, la entiendas y puedas explicarla con tus
palabras. No tienes que memorizar todo el código.

## Parte 1 (5 minutos): sigue las cuatro piezas

Abre en VS Code la carpeta `Producto-2/erick-lab-01` y localiza:

1. `public/index.php`: busca `/aviso/editar`. **Ruta:** ¿cómo sabe PHP si mostrar
   un formulario (GET) o guardar los cambios (POST)?
2. `app/Controllers/SolicitudController.php`: busca `editarFormulario()` y `editar()`.
   **Controlador:** identifica el usuario, verifica CSRF y valida el texto.
3. `app/Models/Solicitud.php`: busca `actualizarDatosCliente()`.
   **Modelo:** lee, comprueba estado/plazo y actualiza MySQL dentro de una transacción.
4. `app/Views/editar-aviso.php`: **Vista:** presenta los campos de la solicitud actual,
   sin ejecutar sentencias SQL.

Pregunta clave: **¿por qué no basta con ocultar el enlace de edición?**
Porque cualquiera puede fabricar manualmente una petición POST. Las restricciones tienen
que comprobarse también en el servidor y durante la operación SQL.

## Parte 2 (10 minutos): ejercicio práctico con datos ficticios

1. Entra en `http://127.0.0.1:8083/registro`; crea una cuenta de prueba con correo
   `@example.test` y contraseña **inventada**, no la tuya.
2. Accede y pide una reparación estándar para **dentro de ocho días**.
3. En `Mis avisos`, abre el nuevo aviso y pulsa **Corregir avería, dirección o teléfono**.
4. Cambia la descripción (por ejemplo, «Grifo de prueba cambiado de habitación»),
   una dirección inventada y un teléfono ficticio. Guarda y vuelve al detalle.
5. Comprueba que aparece una línea de historial **Pendiente → Pendiente**, con la nota
   de actualización. El estado no cambió: se documentó una edición de los datos.
6. Cancela ese aviso (como falta más de 48 h) e intenta volver a editar: no debe dejarte.

No emplees nombres, direcciones ni teléfonos de personas reales. El formulario de edición
**no cambia la cita, el servicio ni el tipo de urgencia**.

## Parte 3 (5 minutos): prueba automática en tu PowerShell

```powershell
Set-Location 'C:\Users\guill\Documents\UOC\FP448-Backend-PHP-MVC\Producto-2\entorno-p2'
docker compose ps
.\probar-edicion-solicitudes-p2.ps1
```

Resultado comprobado previamente con Windows PowerShell 5.1:
`TOTAL_PRUEBAS_EDICION=26/26` y `EDICION_Y_HISTORIAL_BD=PASS`.
Cada repetición crea **dos clientes y dos incidencias inventadas** en MySQL P2.

## Parte 4: defiende lo que has entendido

Responde con tus palabras; luego podemos corregir tus respuestas juntos:

1. ¿Qué diferencia hay entre `GET /aviso/editar` y `POST /aviso/editar`?
2. ¿Qué hace `WHERE id = :id AND cliente_id = :cliente`? ¿Qué pasaría si quitamos
   `cliente_id`?
3. ¿Por qué se utiliza `FOR UPDATE` y `beginTransaction()`?
4. ¿Qué hace `htmlspecialchars` cuando alguien escribe `<script>` en un campo?
5. ¿Por qué se registra una observación en el historial aunque el estado siga
   siendo `Pendiente`?

### Lo que todavía no debes afirmar

No hemos validado un CRUD de administración, uno de técnicos, un calendario compartido
ni el despliegue en AWS. Todavía no hay constancia del código aportado por Paula.
**No entregues esta práctica aislada como si fuera el Producto 2 completo.**

Evidencia y advertencias: `../PRUEBA-EDICION-SOLICITUDES-2026-11-06.md`.
