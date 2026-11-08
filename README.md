# ReparaYa · Producto 2 · BackendArchitects

Proyecto académico del **Grupo 5** de UOC FP.448, con **Erick Chacón González**
y **Paula Gómez del Águila** como integrantes.

> **Estado de esta rama:** `feat/integracion-paula-local` es una **integración
> posterior a la entrega académica**, preparada y probada por Erick con ayuda de IA.
> No es una fusión definitiva acordada por el equipo, no está incorporada a `main`
> y no modifica lo que se presentó originalmente en Canvas.

Repositorio público de referencia:
https://github.com/Erickconestilo/ReparaYa-Producto2-BackendArchitects

## Autoría, coordinación y estado real

- **Erick:** desarrollo de usuarios, acceso, perfil, solicitudes y sus reglas de
  negocio; posteriormente realizó la integración local, adaptación de arquitectura,
  pruebas y documentación de esta rama.
- **Paula:** aportó en su entrega grupal el trabajo relacionado con el ámbito de
  administración, técnicos y calendario. Se estudiaron sus archivos como referencia
  y se **adaptaron/reimplementaron** funciones compatibles con el router PHP MVC
  y el esquema PDO/MySQL de esta copia. No afirmar que sus archivos se copiaron
  íntegramente o que todo su sistema quedó fusionado.
- **Motivo del avance autónomo:** según Erick, por los horarios diferentes no
  consiguieron mantener una comunicación fluida y, cuando él continuó la integración,
  todavía no había recibido respuesta de Paula. Esta nota explica la coordinación,
  **no cuestiona el trabajo ni la dedicación de Paula**.
- El trabajo de integración y las pruebas se hicieron con apoyo de IA, bajo
  indicaciones de Erick; el historial Git conserva los commits realizados por él.
  El resultado debe ser comprendido y revisado por sus integrantes.

**Autoría de los cambios posteriores:** la integración actual y los nuevos
desarrollos que Erick realice por su cuenta se atribuirán a **Erick**, indicando
el apoyo de IA cuando corresponda. Los aportes verificables de Paula seguirán
reconocidos como suyos, pero **no se le adjudicarán funciones o cambios nuevos
que no haya realizado**. Si vuelve a colaborar, cada aportación futura se
documentará según su autor real. De momento se desconoce si continuará
participando; la falta de respuesta no equivale a una renuncia.

Se validaron **155/155 comprobaciones automatizadas** (68 de usuarios/solicitudes/
edición y 87 de integración) y un recorrido manual de administrador a técnico.
La interfaz visual sigue siendo básica, no se ha verificado AWS y no se puede
considerar un producto terminado.

Antes de publicar esta rama, fusionarla o presentarla como versión conjunta
definitiva, Erick decidirá el siguiente paso respecto a su trabajo y se
procurará revisar con Paula qué partes de su entrega desea incorporar o
corregir. La decisión de continuar desarrollando por separado no requiere
atribuir a Paula la autoría de las mejoras posteriores.

Detalle y cronología: `documentacion/INTEGRACION-AUTONOMA-ERICK-20261108.md`,
`documentacion/INTEGRACION-LOCAL-FASE1-20261108.md`,
`documentacion/INTEGRACION-LOCAL-FASE2-20261108.md` y
`documentacion/VALIDACION-DOCKER-NAVEGADOR-20261108.md`.

## Estructura

- `erick-lab-01/public/index.php` — entrada HTTP y rutas.
- `erick-lab-01/app/Controllers` — validación y casos de uso.
- `erick-lab-01/app/Models` — PDO y SQL preparado.
- `erick-lab-01/app/Views` — HTML con salida escapada.
- `SQL-oficial-original/bbddReparaYa(1).sql` — SQL del docente **intacto**.
- `migraciones/` — ampliación propia, catálogo ficticio y corrección de codificación.
- `entorno-p2/` — Docker, configuración de ejemplo y pruebas.
- `ESTADO-ACTUAL.md` y `PLAN-CIERRE-P2-08-11.md` — continuidad y prioridades.

## Arranque desde una copia nueva (Windows)

Requisitos: Docker Desktop con motor iniciado y PowerShell 5.1 o 7.
No necesitas una imagen construida en Producto 1.

```powershell
Set-Location .\entorno-p2
.\preparar-env-local.ps1
docker compose up -d --build
docker compose ps
```

Web de **esta rama aislada**: http://127.0.0.1:8084/
El puerto 8083 corresponde al entorno original de Erick y no debe confundirse.

Al crear **por primera vez** una base vacía, Docker importa el SQL original, después
la ampliación del esquema, el catálogo **DEMO** y la normalización de `Estándar`.
La importación inicial no se repite al reiniciar una base que ya contiene datos.
El archivo `.env` se genera localmente y **no se sube a GitHub**.
Esta instalación desde cero se probó el 06/11 con una base temporal aislada:
cinco tablas, catálogo DEMO y acentuación del `ENUM` correctos. Al concluir se
eliminaron únicamente el contenedor y el volumen **temporales de esa prueba**;
la base P2 de Erick se conservó sin interrupciones.

Pruebas funcionales (crean usuarios y avisos ficticios dentro de la base P2):

```powershell
.\probar-flujo-p2.ps1
.\probar-solicitudes-p2.ps1
.\probar-edicion-solicitudes-p2.ps1
```

Resultados de referencia verificados en Windows PowerShell 5.1 el 06/11:
`22/22`, `20/20`, `26/26`, todos con salida 0 y comprobación de MySQL real.
No usar correos ni contraseñas reales en las pruebas.

**Nunca ejecutar `docker compose down -v` sobre un entorno con datos que quieras conservar**:
elimina también el volumen de MySQL. Para pausar, usa `docker compose stop`.

## Trabajo en equipo

1. Antes de editar una tabla/ruta de otro integrante, acordar el contrato.
2. Crear ramas como `erick/usuarios-solicitudes` o `paula/admin-tecnicos`.
3. Hacer commits pequeños con autor real y presentar un *pull request* a `main`.
4. Revisar SQL, requisitos, pruebas y autoría antes de fusionar.
5. No versionar `.env`, credenciales ni capturas personales sin revisión.

Guía de entrada para Paula: `GUIA-COLABORACION-PAULA.md`.

## Apoyo para la defensa y la documentación

- `documentacion/DEFENSA-TECNICA-ERICK-P2.md` — guion de menos de dos minutos, archivos reales, preguntas y ensayo.
- `documentacion/GUIA-ARCHIFY-Y-MAPA-MANUAL-P2.md` — dos diagramas HTML, alcance verificado y guía para dibujar el mapa obligatorio a mano.
- `documentacion/SECCION-WORD-ERICK-P2.md` — texto base de la aportación de Erick para revisar con su propia voz.
- Los diagramas Archify están en `.archify/architecture-reparaya-20261107-072710/` y `.archify/lifecycle-incidencias-20261107-072710/`. Son **material de apoyo**, no sustituyen el mapa manuscrito ni acreditan funciones pendientes.

Esta rama conserva el crédito del trabajo de ambos y documenta que Erick
ejecutó por su cuenta la integración posterior ante la falta de una respuesta
oportuna para coordinarla. Queda pendiente una revisión conjunta.
