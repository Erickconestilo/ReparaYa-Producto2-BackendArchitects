# Produce credenciales LOCALES aleatorias; no las muestra ni sobrescribe una .env existente.
# Se conserva como procedimiento reproducible, no como credencial.
$ErrorActionPreference = 'Stop'
$destino = Join-Path $PSScriptRoot '.env'
if (Test-Path -LiteralPath $destino) {
    Write-Output 'La configuración .env ya existe; no se modifica.'
    exit 0
}

$rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
function NuevaClave() {
    $bytes = New-Object byte[] 24
    $rng.GetBytes($bytes)
    return [Convert]::ToBase64String($bytes).TrimEnd('=').Replace('+', 'A').Replace('/', 'B')
}

$claveAplicacion = NuevaClave
$claveRoot = NuevaClave
$contenido = @(
    'MYSQL_DATABASE=reparaya_p2_integracion'
    'MYSQL_USER=reparaya_p2_integracion'
    "MYSQL_PASSWORD=$claveAplicacion"
    "MYSQL_ROOT_PASSWORD=$claveRoot"
) -join "`n"
[System.IO.File]::WriteAllText($destino, $contenido + "`n", [System.Text.UTF8Encoding]::new($false))
$rng.Dispose()
Write-Output 'Configuración .env local creada con dos claves aleatorias diferentes (valores ocultos).'
