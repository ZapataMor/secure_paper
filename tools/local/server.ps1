param([ValidateSet('iniciar', 'detener')][string]$Accion = 'iniciar')
$ErrorActionPreference = 'Stop'
$root = [IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..\..'))
$php = Join-Path $root '.local\php\php.exe'
$statePath = Join-Path $root '.local\servidor-local.json'
$logRoot = Join-Path $root '.local\logs'

try {
    if ($Accion -eq 'detener') {
        if (-not (Test-Path -LiteralPath $statePath)) {
            Write-Host 'No hay un servidor local registrado.'
            exit 0
        }
        $state = Get-Content -LiteralPath $statePath -Raw | ConvertFrom-Json
        $process = Get-Process -Id $state.pid -ErrorAction SilentlyContinue
        if ($null -eq $process) {
            Write-Host 'El servidor local ya esta detenido.'
            exit 0
        }
        $recordedStart = if ($state.startedAtUtc -is [DateTime]) { $state.startedAtUtc } else {
            [DateTime]::Parse($state.startedAtUtc, [Globalization.CultureInfo]::InvariantCulture, [Globalization.DateTimeStyles]::RoundtripKind)
        }
        $expectedTime = $recordedStart.ToUniversalTime().Ticks
        if ($process.Path -ne $php -or $process.StartTime.ToUniversalTime().Ticks -ne $expectedTime) {
            throw 'El proceso registrado no coincide con este servidor. No se detuvo ningun proceso.'
        }
        Stop-Process -Id $process.Id
        Write-Host 'Propaps local se detuvo. Los datos de prueba se conservan en D.'
        exit 0
    }

    if (-not (Test-Path -LiteralPath $php)) {
        throw 'Falta PHP local. Consulta docs\entorno-local.md.'
    }
    Push-Location $root
    try {
        & $php (Join-Path $PSScriptRoot 'check.php')
        if ($LASTEXITCODE -ne 0) { throw 'La configuracion no paso la comprobacion local.' }
    } finally { Pop-Location }
    foreach ($needed in @('vendor\autoload.php', 'public\build\manifest.json', '.local\database.sqlite')) {
        if (-not (Test-Path -LiteralPath (Join-Path $root $needed))) { throw "Falta $needed; completa la preparacion local." }
    }
    if (Get-NetTCPConnection -LocalPort 8000 -State Listen -ErrorAction SilentlyContinue) {
        Write-Host 'El puerto 8000 ya esta en uso. Si ya iniciaste Propaps, abre http://127.0.0.1:8000.'
        exit 0
    }
    New-Item -ItemType Directory -Path $logRoot -Force | Out-Null
    $env:TEMP = Join-Path $root '.local\tmp'
    $env:TMP = $env:TEMP
    # PHP debe recibir mas de 10 archivos para que Laravel rechace el lote completo, sin truncarlo.
    $server = Start-Process -FilePath $php -ArgumentList @('-d', 'max_file_uploads=20', '-S', '127.0.0.1:8000', '../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php') -WorkingDirectory (Join-Path $root 'public') -WindowStyle Hidden -RedirectStandardOutput (Join-Path $logRoot 'servidor-local.log') -RedirectStandardError (Join-Path $logRoot 'servidor-local-error.log') -PassThru
    $state = [pscustomobject]@{
        pid = $server.Id
        php = $php
        startedAtUtc = $server.StartTime.ToUniversalTime().ToString('o')
        url = 'http://127.0.0.1:8000'
    }
    $state | ConvertTo-Json | Set-Content -LiteralPath $statePath -Encoding UTF8
    $ready = $false
    for ($attempt = 0; $attempt -lt 10; $attempt++) {
        try {
            $response = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/login' -UseBasicParsing -TimeoutSec 3
            if ([int]$response.StatusCode -eq 200) { $ready = $true; break }
        } catch { Start-Sleep -Milliseconds 250 }
    }
    if (-not $ready) { throw 'El proceso inicio, pero la pagina no respondio correctamente. Revisa .local\logs.' }
    Write-Host 'Propaps local esta disponible en http://127.0.0.1:8000'
    Write-Host 'Puedes cerrar esta ventana. Usa DETENER-LOCAL.cmd cuando termines.'
} catch {
    Write-Host ('No se completo la accion: ' + $_.Exception.Message)
    exit 1
}