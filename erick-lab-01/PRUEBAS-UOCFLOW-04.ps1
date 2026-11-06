# Pruebas del laboratorio ReparaYa: NO crean usuarios ni usan datos reales.
# Ejecutar desde la terminal PowerShell 7 de erick-lab-01:
# pwsh -NoProfile -File ./PRUEBAS-UOCFLOW-04.ps1
param(
    [string]$BaseUrl = 'http://127.0.0.1:8082'
)

$ErrorActionPreference = 'Stop'
$script:fallos = 0
$script:total = 0

function Comprobar-HTTP {
    param(
        [string]$Nombre,
        [string]$Metodo,
        [string]$Ruta,
        [int]$EstadoEsperado,
        [string]$TextoEsperado,
        [AllowNull()][string]$Datos = $null
    )

    $script:total++
    $opciones = @{
        Uri                = "$BaseUrl$Ruta"
        Method             = $Metodo
        SkipHttpErrorCheck = $true
        ErrorAction        = 'Stop'
    }

    if ($null -ne $Datos) {
        $opciones.Body = $Datos
        $opciones.ContentType = 'application/x-www-form-urlencoded'
    }

    try {
        $respuesta = Invoke-WebRequest @opciones
        $estado = [int]$respuesta.StatusCode
        $contenido = [string]$respuesta.Content
        $correcto = ($estado -eq $EstadoEsperado) -and
                    $contenido.Contains($TextoEsperado)

        if ($correcto) {
            Write-Host "PASS $Nombre (HTTP $estado)" -ForegroundColor Green
        } else {
            $script:fallos++
            Write-Host "FAIL $Nombre (HTTP $estado; esperado $EstadoEsperado; texto esperado: $TextoEsperado)" -ForegroundColor Red
        }
    } catch {
        $script:fallos++
        Write-Host "FAIL $Nombre (no se pudo realizar la petición: $($_.Exception.Message))" -ForegroundColor Red
    }
}

Comprobar-HTTP -Nombre 'Inicio' -Metodo GET -Ruta '/' -EstadoEsperado 200 -TextoEsperado 'Laboratorio MVC'
Comprobar-HTTP -Nombre 'Formulario' -Metodo GET -Ruta '/registro' -EstadoEsperado 200 -TextoEsperado 'Crear cuenta en ReparaYa'
Comprobar-HTTP -Nombre 'Ruta inexistente' -Metodo GET -Ruta '/no-existe' -EstadoEsperado 404 -TextoEsperado '404'
Comprobar-HTTP -Nombre 'POST sin datos' -Metodo POST -Ruta '/registro' -EstadoEsperado 422 -TextoEsperado 'nombre' -Datos ''
Comprobar-HTTP -Nombre 'Correo invalido' -Metodo POST -Ruta '/registro' -EstadoEsperado 422 -TextoEsperado 'correo' -Datos 'nombre=Alumno+Prueba&correo=invalido&clave=ClaveFicticia123'
Comprobar-HTTP -Nombre 'Clave demasiado corta' -Metodo POST -Ruta '/registro' -EstadoEsperado 422 -TextoEsperado 'contraseña' -Datos 'nombre=Alumno+Prueba&correo=alumno%40example.test&clave=abc'
Comprobar-HTTP -Nombre 'Datos ficticios validos' -Metodo POST -Ruta '/registro' -EstadoEsperado 200 -TextoEsperado 'No se ha creado ninguna cuenta' -Datos 'nombre=Alumno+Prueba&correo=alumno%40example.test&clave=ClaveFicticia123'
Comprobar-HTTP -Nombre 'Metodo no permitido' -Metodo PUT -Ruta '/registro' -EstadoEsperado 405 -TextoEsperado 'Método no permitido'

$correctos = $script:total - $script:fallos
Write-Host "Resultado: $correctos/$($script:total) pruebas correctas."
if ($script:fallos -gt 0) { exit 1 }
exit 0
