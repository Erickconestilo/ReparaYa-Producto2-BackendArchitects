# Repite el flujo real con SOLO datos ficticios; deja una cuenta de prueba en MySQL P2.
$ErrorActionPreference = 'Stop'
# Windows PowerShell 5.1 no carga System.Net.Http automáticamente.
# PowerShell 7 sí, pero lo cargamos explícitamente para admitir ambas versiones.
Add-Type -AssemblyName System.Net.Http -ErrorAction Stop
$base = 'http://127.0.0.1:8084'
function CrearCliente() {
    $controlador = [System.Net.Http.HttpClientHandler]::new()
    $controlador.AllowAutoRedirect = $false
    $controlador.UseCookies = $true
    $controlador.CookieContainer = [System.Net.CookieContainer]::new()
    return [System.Net.Http.HttpClient]::new($controlador)
}
$sesion = CrearCliente
$anonimo = CrearCliente
$correo = "alumno-p2-$(Get-Date -Format yyyyMMddHHmmssfff)@example.test"
$correoNuevo = $correo.Replace('@example.test', '-nuevo@example.test')
$clave = 'Demo-SoloPrueba-12345' # Clave inventada; NUNCA usar una personal.
$claveNueva = 'Demo-Cambiada-98765'
$nombre = 'Alumno de prueba'
$pasadas = 0

function Comprobar([bool] $condicion, [string] $caso) {
    if (-not $condicion) { throw "FAIL: $caso" }
    $script:pasadas++
    Write-Output "PASS: $caso"
}
function Solicitud([string] $ruta, [string] $metodo, $sesionWeb, $datos = $null) {
    $peticion = [System.Net.Http.HttpRequestMessage]::new(
        [System.Net.Http.HttpMethod]::new($metodo), "$base$ruta"
    )
    if ($null -ne $datos) {
        $diccionario = [System.Collections.Generic.Dictionary[string,string]]::new()
        foreach ($claveDato in $datos.Keys) {
            $diccionario.Add([string] $claveDato, [string] $datos[$claveDato])
        }
        $peticion.Content = [System.Net.Http.FormUrlEncodedContent]::new($diccionario)
    }
    try {
        $respuesta = $sesionWeb.SendAsync($peticion).GetAwaiter().GetResult()
        try {
            return [pscustomobject]@{
                StatusCode = [int] $respuesta.StatusCode
                Headers = @{Location = [string] $respuesta.Headers.Location}
                Content = $respuesta.Content.ReadAsStringAsync().GetAwaiter().GetResult()
            }
        } finally {
            $respuesta.Dispose()
        }
    } finally {
        $peticion.Dispose()
    }
}
function Token($respuesta) {
    if ($respuesta.Content -notmatch 'name="_csrf" value="([a-f0-9]{64})"') {
        throw 'No se encontró token CSRF en el formulario.'
    }
    return $Matches[1]
}

$inicio = Solicitud '/' 'GET' $sesion
Comprobar ($inicio.StatusCode -eq 200 -and $inicio.Content -match 'ReparaYa') 'GET inicio (200)'
$registro = Solicitud '/registro' 'GET' $sesion
$tokenRegistro = Token $registro
Comprobar ($registro.StatusCode -eq 200) 'GET registro y token CSRF (200)'

$sinToken = Solicitud '/registro' 'POST' $anonimo @{nombre=$nombre;correo=$correo;clave=$clave}
Comprobar ($sinToken.StatusCode -eq 403) 'POST sin token rechazado (403)'
$invalido = Solicitud '/registro' 'POST' $sesion @{_csrf=$tokenRegistro; nombre='';correo='incorrecto';clave='123'}
Comprobar ($invalido.StatusCode -eq 422) 'Validación en servidor (422)'

$alta = Solicitud '/registro' 'POST' $sesion @{_csrf=$tokenRegistro;nombre=$nombre;correo=$correo;clave=$clave}
Comprobar ($alta.StatusCode -eq 303 -and $alta.Headers.Location -like '/login*') 'Alta real y redirección (303)'
$duplicado = Solicitud '/registro' 'POST' $sesion @{_csrf=$tokenRegistro;nombre=$nombre;correo=$correo;clave=$clave}
Comprobar ($duplicado.StatusCode -eq 409) 'Correo único: duplicado rechazado (409)'

$login = Solicitud '/login' 'GET' $sesion
$tokenLogin = Token $login
$fallo = Solicitud '/login' 'POST' $sesion @{_csrf=$tokenLogin;correo=$correo;clave='ClaveFicticiaIncorrecta'}
Comprobar ($fallo.StatusCode -eq 401) 'Contraseña incorrecta no inicia sesión (401)'
$sinAcceso = Solicitud '/perfil' 'GET' $anonimo
Comprobar ($sinAcceso.StatusCode -eq 302) 'Perfil sin sesión redirige (302)'
$acceso = Solicitud '/login' 'POST' $sesion @{_csrf=$tokenLogin;correo=$correo;clave=$clave}
Comprobar ($acceso.StatusCode -eq 303 -and $acceso.Headers.Location -eq '/perfil') 'Login seguro (303)'

$perfil = Solicitud '/perfil' 'GET' $sesion
$tokenPerfil = Token $perfil
Comprobar ($perfil.StatusCode -eq 200 -and $perfil.Content -match 'Alumno de prueba' -and $perfil.Content -match 'particular') 'Perfil propio con rol (200)'
$editar = Solicitud '/perfil' 'POST' $sesion @{_csrf=$tokenPerfil;nombre='Alumno actualizado';telefono='600000000'}
Comprobar ($editar.StatusCode -eq 303) 'Editar nombre y teléfono (303)'
$perfilNuevo = Solicitud '/perfil' 'GET' $sesion
Comprobar ($perfilNuevo.Content -match 'Alumno actualizado') 'Perfil actualizado leído de MySQL'
$seguridadFallida = Solicitud '/perfil/seguridad' 'POST' $sesion @{_csrf=$tokenPerfil;correo=$correoNuevo;clave_actual='Erronea';clave_nueva=$claveNueva}
Comprobar ($seguridadFallida.StatusCode -eq 403) 'Cambio de acceso exige clave actual (403)'
$seguridad = Solicitud '/perfil/seguridad' 'POST' $sesion @{_csrf=$tokenPerfil;correo=$correoNuevo;clave_actual=$clave;clave_nueva=$claveNueva}
Comprobar ($seguridad.StatusCode -eq 303) 'Actualizar correo y contraseña (303)'
$perfilConCorreo = Solicitud '/perfil' 'GET' $sesion
Comprobar ($perfilConCorreo.Content -match [regex]::Escape($correoNuevo)) 'Nuevo correo visible desde BD'
$salir = Solicitud '/salir' 'POST' $sesion @{_csrf=$tokenPerfil}
Comprobar ($salir.StatusCode -eq 303) 'Cerrar sesión (303)'
$cerrado = Solicitud '/perfil' 'GET' $sesion
Comprobar ($cerrado.StatusCode -eq 302) 'Acceso revocado tras salir (302)'
$nuevoLogin = Solicitud '/login' 'GET' $sesion
$tokenNuevoLogin = Token $nuevoLogin
$correoViejo = Solicitud '/login' 'POST' $sesion @{_csrf=$tokenNuevoLogin;correo=$correo;clave=$clave}
Comprobar ($correoViejo.StatusCode -eq 401) 'Correo antiguo deja de autenticar (401)'
$claveVieja = Solicitud '/login' 'POST' $sesion @{_csrf=$tokenNuevoLogin;correo=$correoNuevo;clave=$clave}
Comprobar ($claveVieja.StatusCode -eq 401) 'Contraseña anterior deja de autenticar (401)'
$loginNuevo = Solicitud '/login' 'POST' $sesion @{_csrf=$tokenNuevoLogin;correo=$correoNuevo;clave=$claveNueva}
Comprobar ($loginNuevo.StatusCode -eq 303) 'Nuevo correo y contraseña permiten entrar (303)'
$noExiste = Solicitud '/no-existe' 'GET' $sesion
Comprobar ($noExiste.StatusCode -eq 404) 'Ruta inexistente sigue devolviendo 404'

docker compose -f (Join-Path $PSScriptRoot 'compose.yaml') exec -T web php tests/comprobar-usuario-ficticio.php $correoNuevo
Comprobar ($LASTEXITCODE -eq 0) 'Hash bcrypt/PASSWORD_DEFAULT verificado en la BD'
Write-Output "TOTAL_PRUEBAS=$pasadas/22"
Write-Output "CUENTA_FICTICIA=$correoNuevo"
