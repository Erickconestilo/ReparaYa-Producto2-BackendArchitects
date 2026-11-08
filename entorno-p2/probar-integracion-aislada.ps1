# Prueba de la integracion en una BD inventada y efimera, sin puertos publicados.
# No utiliza ni elimina contenedores, volumenes o bases de datos del proyecto habitual.
$ErrorActionPreference = 'Stop'
$nombre = 'uoc-p2-integracion-' + [guid]::NewGuid().ToString('N').Substring(0, 8)
$raiz = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$claveTemporal = 'Solo-Prueba-Aislada-2026'
$iniciado = $false

try {
    $montajes = @(
        '--mount', "type=bind,source=$raiz\SQL-oficial-original\bbddReparaYa(1).sql,target=/docker-entrypoint-initdb.d/01_original.sql,readonly",
        '--mount', "type=bind,source=$raiz\migraciones\20261106_01_ampliacion_p2.sql,target=/docker-entrypoint-initdb.d/02_ampliacion.sql,readonly",
        '--mount', "type=bind,source=$raiz\migraciones\20261106_02_datos_demo.sql,target=/docker-entrypoint-initdb.d/03_demo.sql,readonly",
        '--mount', "type=bind,source=$raiz\migraciones\20261106_03_normalizar_enum_urgencia.sql,target=/docker-entrypoint-initdb.d/04_urgencia.sql,readonly",
        '--mount', "type=bind,source=$raiz\migraciones\20261108_05_catalogo_activo.sql,target=/docker-entrypoint-initdb.d/05_catalogo_activo.sql,readonly"
    )
    & docker run -d --name $nombre --network none --tmpfs /var/lib/mysql:rw `
        --env "MYSQL_ROOT_PASSWORD=$claveTemporal" `
        --env MYSQL_DATABASE=uoc_p2_integracion_prueba `
        @montajes mysql:8.0 --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'No pudo iniciarse el MySQL temporal.' }
    $iniciado = $true

    $listo = $false
    for ($intento = 0; $intento -lt 50; $intento++) {
        # En PowerShell 5.1, stderr de un proceso nativo puede elevar un error.
        # La BD aun arrancando es normal: ignoramos solo los fallos de este sondeo.
        $preferenciaAnterior = $ErrorActionPreference
        $ErrorActionPreference = 'SilentlyContinue'
        try {
            $resultado = & docker exec --env "MYSQL_PWD=$claveTemporal" $nombre mysql -uroot `
                -D uoc_p2_integracion_prueba -N -e 'SELECT COUNT(*) FROM especialidades' 2>$null
            $codigoSondeo = $LASTEXITCODE
        } finally {
            $ErrorActionPreference = $preferenciaAnterior
        }
        if ($codigoSondeo -eq 0 -and [int]($resultado | Select-Object -Last 1) -ge 2) {
            $listo = $true
            break
        }
        Start-Sleep -Milliseconds 800
    }
    if (!$listo) { throw 'La base MySQL temporal no completo su inicializacion.' }

    & docker run --rm --network "container:$nombre" --read-only `
        --mount "type=bind,source=$raiz\erick-lab-01,target=/lab,readonly" `
        -w /lab `
        --env DB_HOST=127.0.0.1 `
        --env DB_PORT=3306 `
        --env DB_NAME=uoc_p2_integracion_prueba `
        --env DB_USER=root `
        --env "DB_PASSWORD=$claveTemporal" `
        --env INTEGRATION_TEST_EPHEMERAL=1 `
        reparaya-p2-web:local php tests/comprobar-integracion-equipo-ficticia.php
    if ($LASTEXITCODE -ne 0) { throw 'No supero las pruebas de integracion.' }

    & docker run --rm --network "container:$nombre" --read-only `
        --mount "type=bind,source=$raiz\erick-lab-01,target=/lab,readonly" `
        -w /lab `
        --env DB_HOST=127.0.0.1 `
        --env DB_PORT=3306 `
        --env DB_NAME=uoc_p2_integracion_prueba `
        --env DB_USER=root `
        --env "DB_PASSWORD=$claveTemporal" `
        --env INTEGRATION_TEST_EPHEMERAL=1 `
        reparaya-p2-web:local php tests/comprobar-catalogo-ficticio.php
    if ($LASTEXITCODE -ne 0) { throw 'No supero las pruebas MySQL del catalogo.' }

    # Servidor PHP HTTP solo dentro del espacio de red del contenedor temporal.
    # No se publica ningun puerto en el equipo del usuario.
    & docker run --rm --network "container:$nombre" --read-only --tmpfs /tmp:rw `
        --mount "type=bind,source=$raiz\erick-lab-01,target=/lab,readonly" `
        -w /lab `
        --env DB_HOST=127.0.0.1 `
        --env DB_PORT=3306 `
        --env DB_NAME=uoc_p2_integracion_prueba `
        --env DB_USER=root `
        --env "DB_PASSWORD=$claveTemporal" `
        --env INTEGRATION_TEST_EPHEMERAL=1 `
        reparaya-p2-web:local sh -c 'php -S 127.0.0.1:8000 -t public >/dev/null 2>&1 & serverpid=$!; php tests/comprobar-integracion-http.php; result=$?; kill $serverpid; wait $serverpid 2>/dev/null; exit $result'
    if ($LASTEXITCODE -ne 0) { throw 'No supero las pruebas HTTP del flujo de integracion.' }
} finally {
    if ($iniciado) {
        & docker rm -f $nombre | Out-Null
        Write-Output "BASE_TEMPORAL_ELIMINADA=$nombre"
    }
}
