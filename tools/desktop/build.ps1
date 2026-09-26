param(
    [string]$ProcessorId = "",
    [switch]$SkipFrontendBuild
)

$ErrorActionPreference = "Stop"

$secret = "muktar.desktop.v1.9f3a7c2e1b8d4f6a0c5e7b9d2a4c6e8f"
$appKey = (& php -r "echo 'base64:'.base64_encode(random_bytes(32));").Trim()
$root = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$desktop = Join-Path $root "tools\desktop"
$cache = Join-Path $desktop "cache"
$dist = Join-Path $desktop "dist"
$stage = Join-Path $cache "stage"
$payload = Join-Path $cache "payload"
$phpDir = Join-Path $cache "php"
$csc = "C:\Windows\Microsoft.NET\Framework64\v4.0.30319\csc.exe"

function Get-PhpZipUrl {
    $releases = Invoke-RestMethod -Uri "https://windows.php.net/downloads/releases/releases.json"
    $build = $releases."8.4"."nts-vs17-x64".zip.path
    if (-not $build) {
        throw "Could not find PHP 8.4 NTS x64 zip in windows.php.net releases."
    }
    return "https://windows.php.net/downloads/releases/$build"
}

if (-not (Test-Path $csc)) {
    throw "C# compiler not found: $csc"
}

New-Item -ItemType Directory -Force -Path $cache, $dist, $stage, $payload, $phpDir | Out-Null

function Copy-AppTree {
    $directories = @(
        "app",
        "bootstrap",
        "config",
        "database",
        "public",
        "resources",
        "routes"
    )

    foreach ($directory in $directories) {
        $source = Join-Path $root $directory
        $destination = Join-Path $stage $directory
        if (Test-Path $destination) {
            Remove-Item $destination -Recurse -Force
        }
        Copy-Item $source $destination -Recurse
    }

    foreach ($file in @("artisan", "composer.json", "composer.lock")) {
        Copy-Item (Join-Path $root $file) (Join-Path $stage $file) -Force
    }

    foreach ($name in @("hot", "storage")) {
        $extra = Join-Path $stage "public\$name"
        if (Test-Path $extra) {
            Remove-Item $extra -Recurse -Force
        }
    }

    New-Item -ItemType Directory -Force -Path (Join-Path $stage "storage\app\public") | Out-Null
    New-Item -ItemType Directory -Force -Path (Join-Path $stage "storage\framework\cache\data") | Out-Null
    New-Item -ItemType Directory -Force -Path (Join-Path $stage "storage\framework\sessions") | Out-Null
    New-Item -ItemType Directory -Force -Path (Join-Path $stage "storage\framework\views") | Out-Null
    New-Item -ItemType Directory -Force -Path (Join-Path $stage "storage\logs") | Out-Null
    New-Item -ItemType Directory -Force -Path (Join-Path $stage "bootstrap\cache") | Out-Null
    Get-ChildItem (Join-Path $stage "bootstrap\cache") -File -ErrorAction SilentlyContinue |
        Where-Object { $_.Name -ne ".gitignore" } |
        Remove-Item -Force
}

Write-Host "Building frontend assets..."
if (-not $SkipFrontendBuild) {
    Push-Location $root
    try {
        npm run build
    }
    finally {
        Pop-Location
    }
}

Write-Host "Staging Laravel application..."
if (Test-Path $stage) {
    Remove-Item $stage -Recurse -Force
}
New-Item -ItemType Directory -Force -Path $stage | Out-Null
Copy-AppTree

Write-Host "Installing production PHP dependencies..."
Push-Location $stage
try {
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
}
finally {
    Pop-Location
}

Write-Host "Preparing SQLite seed database..."
$seed = Join-Path $cache "seed.sqlite"
if (Test-Path $seed) {
    Remove-Item $seed -Force
}
New-Item -ItemType File -Path $seed | Out-Null
Push-Location $stage
try {
    $env:DB_CONNECTION = "sqlite"
    $env:DB_DATABASE = ($seed -replace '\\', '/')
    $env:APP_KEY = $appKey
    $env:APP_ENV = "production"
    $env:MUKTAR_DESKTOP = "true"
    & php artisan migrate --force --no-interaction
    if ($LASTEXITCODE -ne 0) { throw "migrate failed" }
    & php artisan db:seed --class=DesktopUserSeeder --force --no-interaction
    if ($LASTEXITCODE -ne 0) { throw "seed failed" }
}
finally {
    Pop-Location
    Remove-Item Env:DB_CONNECTION -ErrorAction SilentlyContinue
    Remove-Item Env:DB_DATABASE -ErrorAction SilentlyContinue
    Remove-Item Env:APP_KEY -ErrorAction SilentlyContinue
    Remove-Item Env:MUKTAR_DESKTOP -ErrorAction SilentlyContinue
}

$phpZip = Join-Path $cache "php-nts.zip"
if (-not (Test-Path (Join-Path $phpDir "php.exe"))) {
    Write-Host "Downloading portable PHP..."
    $phpZipUrl = Get-PhpZipUrl
    Write-Host $phpZipUrl
    Invoke-WebRequest -Uri $phpZipUrl -OutFile $phpZip
    if (Test-Path $phpDir) {
        Remove-Item $phpDir -Recurse -Force
    }
    New-Item -ItemType Directory -Force -Path $phpDir | Out-Null
    Expand-Archive -Path $phpZip -DestinationPath $phpDir -Force
}

Copy-Item (Join-Path $desktop "php.ini.template") (Join-Path $phpDir "php.ini") -Force

foreach ($dll in @("vcruntime140.dll", "vcruntime140_1.dll", "msvcp140.dll")) {
    $from = Join-Path $env:SystemRoot "System32\$dll"
    if (Test-Path $from) {
        Copy-Item $from (Join-Path $phpDir $dll) -Force
    }
}

$iconPath = Join-Path $cache "Muktar.ico"
$manifest = Join-Path $desktop "app.manifest"
Write-Host "Creating application icon..."
python (Join-Path $desktop "generate_icon.py") $iconPath
Copy-Item $iconPath (Join-Path $root "public\favicon.ico") -Force

function Compile-Csharp {
    param(
        [string]$Source,
        [string]$Output,
        [string[]]$References,
        [string]$BoundProcessorId = ""
    )

    $temp = Join-Path $cache ([IO.Path]::GetFileName($Source))
    $code = Get-Content -Raw -Encoding UTF8 $Source
    $code = $code.Replace("{{LICENSE_SECRET}}", $secret)
    $code = $code.Replace("{{BOUND_PROCESSOR_ID}}", $BoundProcessorId)
    $code = $code.Replace("{{APP_KEY}}", $appKey)
    Set-Content -Path $temp -Value $code -Encoding UTF8

    $args = @(
        "/nologo",
        "/optimize+",
        "/target:winexe",
        "/platform:x64",
        "/out:$Output",
        "/win32icon:$iconPath",
        "/win32manifest:$manifest"
    )
    foreach ($reference in $References) {
        $args += "/r:$reference"
    }
    $args += $temp

    & $csc @args
    if ($LASTEXITCODE -ne 0) {
        throw "Failed to compile $Source"
    }
}

$framework = "C:\Windows\Microsoft.NET\Framework64\v4.0.30319"
Write-Host "Compiling launcher and installer..."
Compile-Csharp `
    -Source (Join-Path $desktop "launcher\MuktarLauncher.cs") `
    -Output (Join-Path $cache "Muktar.exe") `
    -References @(
        "$framework\System.Windows.Forms.dll",
        "$framework\System.Drawing.dll",
        "$framework\System.Management.dll"
    )

Compile-Csharp `
    -Source (Join-Path $desktop "setup\MuktarSetup.cs") `
    -Output (Join-Path $cache "MuktarSetupStub.exe") `
    -References @(
        "$framework\System.Windows.Forms.dll",
        "$framework\System.Drawing.dll",
        "$framework\System.Management.dll",
        "$framework\System.IO.Compression.dll",
        "$framework\System.IO.Compression.FileSystem.dll"
    )

Compile-Csharp `
    -Source (Join-Path $desktop "setup\ReadProcessorId.cs") `
    -Output (Join-Path $dist "MuktarCpuId.exe") `
    -References @(
        "$framework\System.Windows.Forms.dll",
        "$framework\System.Drawing.dll",
        "$framework\System.Management.dll"
    )

Compile-Csharp `
    -Source (Join-Path $desktop "setup\MuktarBinder.cs") `
    -Output (Join-Path $dist "MuktarBinder.exe") `
    -References @(
        "$framework\System.Windows.Forms.dll",
        "$framework\System.Drawing.dll"
    )

# Keep legacy name for older links
Copy-Item (Join-Path $dist "MuktarCpuId.exe") (Join-Path $dist "ReadProcessorId.exe") -Force

Write-Host "Packaging payload..."
if (Test-Path $payload) {
    Remove-Item $payload -Recurse -Force
}
New-Item -ItemType Directory -Force -Path $payload | Out-Null
Copy-Item (Join-Path $cache "Muktar.exe") (Join-Path $payload "Muktar.exe")
Copy-Item $iconPath (Join-Path $payload "Muktar.ico")
Copy-Item $phpDir (Join-Path $payload "php") -Recurse
Copy-Item $stage (Join-Path $payload "app") -Recurse
Copy-Item (Join-Path $cache "seed.sqlite") (Join-Path $payload "seed.sqlite") -Force

Get-ChildItem (Join-Path $payload "app") -Recurse -File | Where-Object {
    $_.Name -eq ".env" -or $_.Name -eq "hot"
} | Remove-Item -Force

$payloadZip = Join-Path $cache "payload.zip"
if (Test-Path $payloadZip) {
    Remove-Item $payloadZip -Force
}
Add-Type -AssemblyName System.IO.Compression.FileSystem
[IO.Compression.ZipFile]::CreateFromDirectory($payload, $payloadZip)

$final = Join-Path $dist "MuktarSetup.exe"
$stubBytes = [IO.File]::ReadAllBytes((Join-Path $cache "MuktarSetupStub.exe"))
$zipBytes = [IO.File]::ReadAllBytes($payloadZip)
$marker = [Text.Encoding]::ASCII.GetBytes("MUKTAR_PAYLOAD_V1")
$length = [BitConverter]::GetBytes([int64]$zipBytes.Length)

$stream = [IO.File]::Open($final, [IO.FileMode]::Create, [IO.FileAccess]::Write)
try {
    $stream.Write($stubBytes, 0, $stubBytes.Length)
    $stream.Write($marker, 0, $marker.Length)
    $stream.Write($length, 0, $length.Length)
    $stream.Write($zipBytes, 0, $zipBytes.Length)
}
finally {
    $stream.Dispose()
}

New-Item -ItemType Directory -Force -Path (Join-Path $dist "customers") | Out-Null

Write-Host ""
Write-Host "Installer (master, stays on your PC): $final"
Write-Host "Customer CPU ID tool: $(Join-Path $dist 'MuktarCpuId.exe')"
Write-Host "Seller binder tool:   $(Join-Path $dist 'MuktarBinder.exe')"
Write-Host "Bound customer copies go to: $(Join-Path $dist 'customers')"
Write-Host "Master installer does NOT self-delete when run from tools\desktop\dist\"
