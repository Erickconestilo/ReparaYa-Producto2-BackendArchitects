# ReparaYa · Producto 2 · BackendArchitects

Trabajo académico del **Grupo 5** en UOC FP.448 — Desarrollo back-end con PHP,
framework MVC y gestión de contenidos. **Participantes actuales:** Erick Chacón González
y Paula Gómez del Águila. **Entrega indicada en Canvas:** 08/11/2026 a las 23:59
(hora de Madrid). Este repositorio es **privado** y se utiliza para colaborar; no
acredita que la actividad haya sido entregada.

Repositorio: https://github.com/Erickconestilo/ReparaYa-Producto2-BackendArchitects
Propietario: `Erickconestilo`. Creado el 06/11/2026. La invitación a Paula está
pendiente de confirmar su **usuario de GitHub exacto**.

## Estado y reparto

- **Erick:** registro/login/perfil de clientes y creación, consulta, cancelación,
  historial y corrección de datos de solicitudes. Funciones probadas sobre MySQL local.
- **Paula:** administración/calendario y técnicos/servicios, **reparto propuesto
  pendiente de confirmar e integrar**. No hay código de Paula en esta primera versión.
- La ampliación de código y las pruebas automatizadas de 06/11 fueron implementadas
  con ayuda de IA bajo autorización de Erick. Él ha ejecutado personalmente
  22/22 pruebas de usuarios y 20/20 de solicitudes; las 26 pruebas de edición fueron
  ejecutadas inicialmente por el asistente. Se debe revisar y defender el código
  individualmente. Véanse los informes y las guías UocStudy.

**No es Producto 2 completo:** faltan los módulos del equipo, roles integrados,
calendario, Git de ambos, AWS, documentación final, mapa, vídeo y entrega.

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

Web local: http://127.0.0.1:8083/

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

Esta copia inicial no sustituye el trabajo ni las decisiones de Paula. El objetivo
es que ambos puedan construir y comprobar la misma versión del proyecto.
