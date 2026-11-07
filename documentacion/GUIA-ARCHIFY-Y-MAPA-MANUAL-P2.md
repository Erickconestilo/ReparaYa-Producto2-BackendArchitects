# Archify y mapa a mano · ReparaYa P2

## Qué hemos añadido

Se descargó **Archify v3.0.1**, etiqueta estable de [`tt-a1i/archify`](https://github.com/tt-a1i/archify), a una carpeta **externa al repositorio académico**, junto a la carpeta Producto-2:

`..\_herramientas\archify-v3.0.1`

No se ha instalado globalmente ni se ha añadido como dependencia de PHP. Se usa como generador local de diagramas. Las salidas HTML están en este repositorio para que Paula pueda abrirlas sin instalar nada.

1. **Arquitectura MVC real, respaldada por fuentes del repo:**
   `.archify/architecture-reparaya-20261107-072710/mvc-reparaya.html`
2. **Estados de incidencia exigidos por la actividad:**
   `.archify/lifecycle-incidencias-20261107-072710/estados-incidencia.html`

Abre los `.html` con el navegador. Son archivos autónomos, no una web desplegada. Archify puede exportar imágenes desde su menú **Export**. Para el Word, conviene guardar una imagen PNG, revisar la legibilidad y añadir un pie explicativo.

## Lo que representan (sin exageraciones)

- El diagrama **MVC** refleja el commit `3d68c51c63d328608605c1f896d9c502a2005be3` del repo y contiene referencias verificadas a líneas de código; muestra rutas, controladores, modelos, vistas, PDO y MySQL.
- El diagrama de **estados** describe el comportamiento que **pide el profesor**. Las fases Asignada → En curso → Finalizada y la asignación por administrador están **pendientes de integración** con el trabajo de Paula. Es un objetivo funcional, **no una prueba de que ya existe**.
- No se ha modificado el SQL oficial, las migraciones, los controladores, el entorno Docker ni los datos MySQL.

## Validación realizada

El generador superó en ambos casos `validate`, `deliver`, verificación de integridad y `browser-check`. Se generaron capturas en temas claro y oscuro y se revisó visualmente la disposición. Las capturas y recibos técnicos quedan solo en la copia local, no en el material académico publicado.

Archify tiene controles de interfaz en inglés; las etiquetas del proyecto y los comentarios están en español. Este hecho no afecta al código PHP.

## Cómo dibujar el mapa obligatorio a mano

**El profesor no acepta que un gráfico digital sustituya esta fotografía.** Hazlo en papel y tómale una foto clara.

En una hoja horizontal, puedes dividir el dibujo en dos partes:

### Parte superior · MVC de ReparaYa

1. Dibuja un **Cliente / navegador** a la izquierda.
2. Flecha **GET/POST** hasta **`public/index.php` / rutas**.
3. Divide en dos cajas: **UsuarioController** (registro/login/perfil) y **SolicitudController** (solicitudes).
4. Desde cada controlador dibuja su **Modelo** correspondiente (`Usuario.php` y `Solicitud.php`).
5. De los modelos sale el acceso **PDO → MySQL** (tablas `usuarios`, `incidencias`, `especialidades`, `historial_estados`).
6. Arriba o abajo conecta las **Vistas PHP**, que muestran formularios y resultados al usuario.
7. Añade una frase corta: **«El controlador valida; el modelo consulta; la vista presenta»**.

### Parte inferior · Estados y roles

1. **Pendiente**: lo crea el cliente al solicitar la reparación.
2. Flecha **administrador asigna técnico** → **Asignada**.
3. Flecha **técnico inicia** → **En curso**.
4. Flecha **técnico finaliza** → **Finalizada**.
5. Desde **Pendiente** y **Asignada** dibuja flechas hacia **Cancelada**: cliente si faltan más de 48 h; administrador mientras el estado lo permita.
6. Rodea **Finalizada** y **Cancelada** y escribe «sin vuelta a estados activos».
7. Nota lateral: **«Historial: cada cambio registra estado anterior, nuevo, actor y fecha»**.

Foto recomendada: luz natural, cámara paralela al papel, sin sombras; revisa que todos los nombres se puedan leer. **El mapa debe hacerlo realmente Erick o Paula a mano**, según acordéis.

## Para estudiar sin memorizar palabras raras

Compara cada flecha de Archify con un archivo real. Si no sabes encontrarla en el código, apunta la duda y revísala antes de la defensa.

## Reproducibilidad y privacidad

El código de Archify no está copiado al proyecto. Se conserva aparte una copia de su versión estable; para regenerar los diagramas, sigue su README oficial y ejecuta `finalize` sobre los `candidate.json` con Node.js >=18, pasando `--repo-root .` en el diagrama MVC. Se desactivó la consulta opcional de actualizaciones con `ARCHIFY_UPDATE_CHECK_DISABLED=1`.

No subas `.env`, credenciales AWS, datos de compañeros ni material privado a servicios externos para elaborar diagramas. El HTML de arquitectura enlaza al repositorio privado; solo tendrán acceso a esos enlaces las personas autorizadas en GitHub.
