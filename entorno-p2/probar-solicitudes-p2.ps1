# HTTP real contra P2; deja dos incidencias y dos cuentas inventadas en BD P2.
$ErrorActionPreference = 'Stop'
# Compatible con Windows PowerShell 5.1 y PowerShell 7.
Add-Type -AssemblyName System.Net.Http -ErrorAction Stop
$base = 'http://127.0.0.1:8083'
$handler = [System.Net.Http.HttpClientHandler]::new()
$handler.AllowAutoRedirect = $false
$handler.UseCookies = $true
$handler.CookieContainer = [System.Net.CookieContainer]::new()
$cliente = [System.Net.Http.HttpClient]::new($handler)
$pases = 0
$identificador = Get-Date -Format yyyyMMddHHmmssfff
$correo = "incidencias-$identificador@example.test"
$clave = 'Demo-SoloPrueba-12345'

function Probar([bool] $condicion, [string] $descripcion) {
    if (!$condicion) { throw "FAIL: $descripcion" }
    $script:pases++
    Write-Output "PASS: $descripcion"
}
function Http([string] $ruta, [string] $metodo = 'GET', $datos = $null) {
    $req = [System.Net.Http.HttpRequestMessage]::new([System.Net.Http.HttpMethod]::new($metodo), "$base$ruta")
    if ($null -ne $datos) {
        $campos = [System.Collections.Generic.Dictionary[string,string]]::new()
        foreach ($nombreCampo in $datos.Keys) { $campos.Add([string] $nombreCampo, [string] $datos[$nombreCampo]) }
        $req.Content = [System.Net.Http.FormUrlEncodedContent]::new($campos)
    }
    try {
        $res = $cliente.SendAsync($req).GetAwaiter().GetResult()
        try {
            return [pscustomobject]@{
                Status = [int]$res.StatusCode
                Location = [string]$res.Headers.Location
                Body = $res.Content.ReadAsStringAsync().GetAwaiter().GetResult()
            }
        } finally { $res.Dispose() }
    } finally { $req.Dispose() }
}
function Token($respuesta) {
    if ($respuesta.Body -notmatch 'name="_csrf" value="([a-f0-9]{64})"') { throw 'Token CSRF ausente.' }
    return $Matches[1]
}

$reg = Http '/registro'
$token = Token $reg
$nuevo = Http '/registro' 'POST' @{_csrf=$token;nombre='Cliente ficticio';correo=$correo;clave=$clave}
Probar ($nuevo.Status -eq 303) 'Alta cliente de ensayo (303)'
$login = Http '/login'
$loginToken = Token $login
$acceso = Http '/login' 'POST' @{_csrf=$loginToken;correo=$correo;clave=$clave}
Probar ($acceso.Status -eq 303) 'Cliente inicia sesión (303)'
$form = Http '/solicitar'
$csrf = Token $form
Probar ($form.Status -eq 200 -and $form.Body -match 'Fontaner' -and $form.Body -match 'Estándar') 'Formulario y especialidades de prueba (200)'
if ($form.Body -notmatch '<option value="([0-9]+)">') { throw 'Falta un servicio DEMO.' }
$servicioId = $Matches[1]

$cerca = (Get-Date).AddDays(1).ToString('yyyy-MM-dd')
$lejos = (Get-Date).AddDays(7).ToString('yyyy-MM-dd')
$datos = @{ _csrf=$csrf; servicio=$servicioId; descripcion='Grifo ficticio que gotea desde ayer'; direccion='Calle Inventada 123, DEMO'; telefono='600000000'; fecha=$cerca; franja='manana'; urgencia='Estándar' }
$estandarPronto = Http '/solicitar' 'POST' $datos
Probar ($estandarPronto.Status -eq 422 -and $estandarPronto.Body -match '48 horas') 'Estándar con menos de 48 h rechazada (422)'
$datos.urgencia = 'Urgente'
$urgente = Http '/solicitar' 'POST' $datos
Probar ($urgente.Status -eq 303 -and $urgente.Location -match 'creada=') 'Urgente antes de 48 h aceptada (303)'
$codigoUrgente = [uri]::UnescapeDataString(($urgente.Location -split 'creada=')[1])
$datos.fecha = $lejos
$datos.urgencia = 'Estándar'
$estandar = Http '/solicitar' 'POST' $datos
Probar ($estandar.Status -eq 303 -and $estandar.Location -match 'creada=') 'Estándar después de 48 h aceptada (303)'
$codigoEstandar = [uri]::UnescapeDataString(($estandar.Location -split 'creada=')[1])

$listado = Http '/mis-avisos'
$referencias = [regex]::Matches($listado.Body, 'href="/aviso\?id=(\d+)"')
Probar ($listado.Status -eq 200 -and $referencias.Count -eq 2 -and $listado.Body.Contains($codigoEstandar) -and $listado.Body.Contains($codigoUrgente)) 'Mis avisos muestra exactamente las dos solicitudes del cliente'
$idEstandar = $referencias[0].Groups[1].Value
$idUrgente = $referencias[1].Groups[1].Value
$detalle = Http "/aviso?id=$idEstandar"
$tokenDetalle = Token $detalle
Probar ($detalle.Status -eq 200 -and $detalle.Body -match 'Historial de estados') 'Detalle privado e historial inicial (200)'
$noCsrf = Http '/aviso/cancelar' 'POST' @{id=$idEstandar}
Probar ($noCsrf.Status -eq 403) 'Cancelación sin CSRF rechazada (403)'
$cancelado = Http '/aviso/cancelar' 'POST' @{_csrf=$tokenDetalle;id=$idEstandar}
Probar ($cancelado.Status -eq 303) 'Cliente cancela cita que supera 48 h (303)'
$despues = Http "/aviso?id=$idEstandar"
Probar ($despues.Body -match 'Cancelada' -and $despues.Body -match 'Pendiente') 'Cancelación e historial visibles'
$repetir = Http '/aviso/cancelar' 'POST' @{_csrf=$tokenDetalle;id=$idEstandar}
Probar ($repetir.Status -eq 409) 'Estado Cancelada impide segunda cancelación (409)'
$cancelarUrgente = Http '/aviso/cancelar' 'POST' @{_csrf=$tokenDetalle;id=$idUrgente}
Probar ($cancelarUrgente.Status -eq 409) 'Cliente no puede cancelar con 48 h o menos (409)'

$salida = Http '/salir' 'POST' @{_csrf=$tokenDetalle}
Probar ($salida.Status -eq 303) 'Primer cliente cierra sesión'
$segundoCorreo = "otro-incidencias-$identificador@example.test"
$regSegundo = Http '/registro'
$tokenSegundo = Token $regSegundo
$altaSegundo = Http '/registro' 'POST' @{_csrf=$tokenSegundo;nombre='Otro cliente ficticio';correo=$segundoCorreo;clave=$clave}
Probar ($altaSegundo.Status -eq 303) 'Alta del segundo cliente ficticio'
$loginSegundo = Http '/login'
$tokenLoginSegundo = Token $loginSegundo
$entradaSegundo = Http '/login' 'POST' @{_csrf=$tokenLoginSegundo;correo=$segundoCorreo;clave=$clave}
Probar ($entradaSegundo.Status -eq 303) 'Segundo cliente inicia sesión'
$misAvisosSegundo = Http '/mis-avisos'
Probar ($misAvisosSegundo.Status -eq 200 -and $misAvisosSegundo.Body -notmatch [regex]::Escape($codigoUrgente)) 'Segundo cliente no ve avisos ajenos'
$avisoAjeno = Http "/aviso?id=$idEstandar"
Probar ($avisoAjeno.Status -eq 404) 'Segundo cliente no puede leer detalle ajeno (404)'
$tokenClienteSegundo = Token (Http '/solicitar')
$cancelarAjeno = Http '/aviso/cancelar' 'POST' @{_csrf=$tokenClienteSegundo;id=$idUrgente}
Probar ($cancelarAjeno.Status -eq 404) 'Segundo cliente no puede cancelar cita ajena (404)'

# Comprobación SQL auténtica: estado + número de eventos de historial.
docker compose exec -T web php tests/comprobar-solicitudes-ficticias.php $codigoUrgente $codigoEstandar
Probar ($LASTEXITCODE -eq 0) 'Estado e historial almacenados en MySQL'
Write-Output "TOTAL_PRUEBAS_SOLICITUDES=$pases/20"
Write-Output "CODIGO_URGENTE=$codigoUrgente CODIGO_ESTANDAR=$codigoEstandar"
$cliente.Dispose()
