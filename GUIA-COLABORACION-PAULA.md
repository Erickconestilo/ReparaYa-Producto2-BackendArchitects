# Guía para incorporar a Paula · ReparaYa Producto 2

Esta guía permite trabajar juntos sin borrar ni sustituir lo que ya haya hecho
cada integrante. El repositorio es privado y pertenece a `Erickconestilo`:

https://github.com/Erickconestilo/ReparaYa-Producto2-BackendArchitects

## Antes de empezar

1. Paula confirma a Erick su **usuario exacto de GitHub** (no basta con su nombre).
2. Erick envía la invitación como colaboradora con permiso de escritura.
3. Paula acepta la invitación desde GitHub y confirma que ve el README.
4. **Si Paula ya tiene código, repositorio o base de datos**, compartir enlace,
   rama, archivos y estado antes de copiar o fusionar nada. Nunca sobrescribir
   su carpeta actual ni datos existentes.

## Descargar una copia nueva en Windows

En una carpeta **nueva**, una vez aceptada la invitación:

```powershell
git clone https://github.com/Erickconestilo/ReparaYa-Producto2-BackendArchitects.git
Set-Location .\ReparaYa-Producto2-BackendArchitects
git status
```

Para probar Docker únicamente si no interfiere con sus servicios existentes:

```powershell
Set-Location .\entorno-p2
.\preparar-env-local.ps1
docker compose up -d --build
docker compose ps
```

Web: `http://127.0.0.1:8083/`. El comando genera datos DEMO y una BD propia;
no debe apuntar a la base real ni de Paula ni de Erick. **No ejecutar `down -v`**
si interesa conservar información en ese volumen.

## Proponer cambios sin pisar el trabajo del otro

```powershell
git switch -c paula/admin-tecnicos
# Tras guardar y probar exclusivamente los cambios propios:
git add ruta/al/archivo-propio.php
git commit -m "Paula: avance del módulo de técnicos"
git push -u origin paula/admin-tecnicos
```

Desde GitHub abrir un **pull request** a `main`, indicar rutas y tablas afectadas,
pruebas realizadas y dependencias. Erick revisa el cambio con Paula antes de
integrarlo. Evitar subir archivos `.env`, contraseñas, exportaciones de bases
con datos reales y capturas no revisadas.

## Preguntas para coordinar este fin de semana

- ¿Qué parte de administración/calendario y técnicos/servicios existe de verdad?
- ¿En qué carpeta o rama está? ¿Qué puede mostrar funcionando?
- ¿Usa el SQL del profesor sin modificar? ¿Requiere migraciones distintas?
- ¿Quién hará integración, evidencias, Word, vídeo y entrega en Canvas?
- ¿Qué puede enseñar y explicar cada uno en su defensa individual?

**Estado en el momento de escribir:** todavía no se ha enviado la invitación,
porque falta confirmar la identidad de GitHub de Paula. La entrega de P2 sigue
pendiente de verificar.
