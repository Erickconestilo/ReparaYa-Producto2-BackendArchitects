# Prueba HTTP real: crea dos clientes y dos avisos ficticios en la BD P2.
# Compatible con Windows PowerShell 5.1 y PowerShell 7 (archivo UTF-8 BOM).
$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.Net.Http -ErrorAction Stop
$base = 'http://127.0.0.1:8084'
$handler = [System.Net.Http.HttpClientHandler]::new()
$handler.AllowAutoRedirect = $false
$handler.UseCookies = $true
$handler.CookieContainer = [System.Net.CookieContainer]::new()
$cliente = [System.Net.Http.HttpClient]::new($handler)
$pases = 0
$marca = Get-Date -Format yyyyMMddHHmmssfff
$correo = "edicion-$marca@example.test"
$otroCorreo = "otro-edicion-$marca@example.test"
$clave = 'Clave-de-ensayo-12345'

function Probar([bool] $condicion, [string] $descripcion) {
    if (!$condicion) { throw "FAIL: $descripcion" }
    $script:pases++
    Write-Output "PASS: $descripcion"
}
function Http([string] $ruta, [string] $metodo = 'GET', $datos = $null) {
    $req = [System.Net.Http.HttpRequestMessage]::new([System.Net.Http.HttpMethod]::new($metodo), "$base$ruta")
    if ($null -ne $datos) {
        $campos = [System.Collections.Generic.Dictionary[string,string]]::new()
        foreach ($campo in $datos.Keys) { $campos.Add([string] $campo, [string] $datos[$campo]) }
        $req.Content = [System.Net.Http.FormUrlEncodedContent]::new($campos)
    }
    try {
        $res = $cliente.SendAsync($req).GetAwaiter().GetResult()
        try {
            return [pscustomobject]@{
                Status = [int] $res.StatusCode
                Location = [string] $res.Headers.Location
                Body = $res.Content.ReadAsStringAsync().GetAwaiter().GetResult()
            }
        } finally { $res.Dispose() }
    } finally { $req.Dispose() }
}
function Token($respuesta) {
    if ($respuesta.Body -notmatch 'name="_csrf" value="([a-f0-9]{64})"') { throw 'Falta token CSRF.' }
    return $Matches[1]
}

try {
    $csrf = Token (Http '/registro')
    Probar ((Http '/registro' 'POST' @{_csrf=$csrf;nombre='Cliente para edicion';correo=$correo;clave=$clave}).Status -eq 303) 'Alta del primer cliente ficticio'
    $csrf = Token (Http '/login')
    Probar ((Http '/login' 'POST' @{_csrf=$csrf;correo=$correo;clave=$clave}).Status -eq 303) 'Inicio de sesion del cliente'
    $form = Http '/solicitar'
    $csrf = Token $form
    Probar ($form.Status -eq 200 -and $form.Body -match '<option value="([0-9]+)">') 'Formulario ofrece un servicio real de prueba'
    $servicio = ([regex]::Match($form.Body, '<option value="([0-9]+)">')).Groups[1].Value
    $datos = @{
        _csrf=$csrf; servicio=$servicio; descripcion='Grifo antiguo de prueba para corregir'
        direccion='Calle Imaginaria 10'; telefono='600000000'
        fecha=(Get-Date).AddDays(8).ToString('yyyy-MM-dd'); franja='manana'; urgencia='Estándar'
    }
    $estandar = Http '/solicitar' 'POST' $datos
    Probar ($estandar.Status -eq 303) 'Solicitud estandar a ocho dias creada'
    $codigoEstandar = [uri]::UnescapeDataString(($estandar.Location -split 'creada=')[1])
    $datos.fecha = (Get-Date).AddDays(1).ToString('yyyy-MM-dd')
    $datos.urgencia = 'Urgente'
    $urgente = Http '/solicitar' 'POST' $datos
    Probar ($urgente.Status -eq 303) 'Solicitud urgente cercana creada'
    $codigoUrgente = [uri]::UnescapeDataString(($urgente.Location -split 'creada=')[1])

    $avisos = Http '/mis-avisos'
    $ids = [regex]::Matches($avisos.Body, 'href="/aviso\?id=(\d+)"')
    Probar ($avisos.Status -eq 200 -and $ids.Count -eq 2) 'Ambos avisos pertenecen al primer cliente'
    $idUrgente = $ids[0].Groups[1].Value
    $idEstandar = $ids[1].Groups[1].Value
    $formEdicion = Http "/aviso/editar?id=$idEstandar"
    $csrf = Token $formEdicion
    Probar ($formEdicion.Status -eq 200 -and $formEdicion.Body -match 'Grifo antiguo') 'Edicion disponible con datos actuales'
    Probar ((Http '/aviso/editar' 'POST' @{id=$idEstandar}).Status -eq 403) 'Edicion sin CSRF rechazada'
    $nuevos = @{
        _csrf=$csrf; id=$idEstandar
        descripcion='Grifo <script>alert(1)</script> actualizado'
        direccion='Avenida Demo 25'; telefono='699999999'
    }
    $invalidos = $nuevos.Clone()
    $invalidos.descripcion = 'corta'
    Probar ((Http '/aviso/editar' 'POST' $invalidos).Status -eq 422) 'Datos demasiado cortos rechazados'
    $guardado = Http '/aviso/editar' 'POST' $nuevos
    Probar ($guardado.Status -eq 303 -and $guardado.Location -match 'actualizado=si') 'Actualizacion redirige al detalle'
    $detalle = Http "/aviso?id=$idEstandar"
    Probar ($detalle.Status -eq 200 -and $detalle.Body -match '&lt;script&gt;' -and $detalle.Body -notmatch '<script>') 'HTML peligroso se muestra escapado'
    Probar ($detalle.Body -match 'Avenida Demo 25' -and $detalle.Body -match '699999999') 'Direccion y telefono actualizados en el detalle'
    Probar ($detalle.Body -match 'Datos de contacto y avería actualizados') 'Historial informa la edicion'
    Probar ((Http '/aviso/editar' 'POST' $nuevos).Status -eq 303) 'Guardar sin cambios no crea error'
    Probar ((Http "/aviso/editar?id=$idUrgente").Status -eq 409) 'Urgente a menos de 48 horas no se puede editar'
    $nuevos.id = $idUrgente
    Probar ((Http '/aviso/editar' 'POST' $nuevos).Status -eq 409) 'Servidor impide editar urgente cercana'
    $nuevos.id = $idEstandar
    Probar ((Http '/aviso/cancelar' 'POST' @{_csrf=$csrf;id=$idEstandar}).Status -eq 303) 'Cancelar solicitud estandar de ensayo'
    Probar ((Http "/aviso/editar?id=$idEstandar").Status -eq 409) 'No hay formulario de edicion para cancelada'
    Probar ((Http '/aviso/editar' 'POST' $nuevos).Status -eq 409) 'Servidor impide editar cancelada'
    Probar ((Http '/salir' 'POST' @{_csrf=$csrf}).Status -eq 303) 'Primer cliente cierra sesion'

    $csrf = Token (Http '/registro')
    Probar ((Http '/registro' 'POST' @{_csrf=$csrf;nombre='Otro cliente';correo=$otroCorreo;clave=$clave}).Status -eq 303) 'Alta del segundo cliente ficticio'
    $csrf = Token (Http '/login')
    Probar ((Http '/login' 'POST' @{_csrf=$csrf;correo=$otroCorreo;clave=$clave}).Status -eq 303) 'Segundo cliente inicia sesion'
    Probar ((Http "/aviso/editar?id=$idEstandar").Status -eq 404) 'Segundo cliente no puede abrir edicion ajena'
    $csrf = Token (Http '/solicitar')
    $nuevos._csrf = $csrf
    Probar ((Http '/aviso/editar' 'POST' $nuevos).Status -eq 404) 'Segundo cliente no puede modificar aviso ajeno'
    Probar ((Http '/aviso/editar' 'PUT').Status -eq 405) 'Metodo no autorizado rechazado (405)'

    docker compose exec -T web php tests/comprobar-edicion-ficticia.php $codigoEstandar $codigoUrgente
    Probar ($LASTEXITCODE -eq 0) 'Edicion, historiales y estados comprobados en MySQL'
    Write-Output "TOTAL_PRUEBAS_EDICION=$pases/26"
    Write-Output "CODIGOS_DEMO_EDITADO=$codigoEstandar URGENTE=$codigoUrgente"
} finally {
    $cliente.Dispose()
}
