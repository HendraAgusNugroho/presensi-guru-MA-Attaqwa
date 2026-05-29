# Membuat ZIP deploy tanpa file sensitif (.env, .git, dll.)
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
$outZip = Join-Path $root "deploy-presensi-guru.zip"

$excludeDirs = @(
    "node_modules", ".git", "tests", ".idea", ".vscode", ".fleet"
)
$excludeFiles = @(
    ".env", ".env.backup", ".env.production", "deploy-presensi-guru.zip"
)

if (Test-Path $outZip) { Remove-Item $outZip -Force }

$temp = Join-Path $env:TEMP ("presensi-deploy-" + [guid]::NewGuid().ToString())
New-Item -ItemType Directory -Path $temp | Out-Null

try {
    robocopy $root $temp /E /XD $excludeDirs /XF $excludeFiles /NFL /NDL /NJH /NJS | Out-Null
    if ($LASTEXITCODE -ge 8) { throw "robocopy gagal (exit $LASTEXITCODE)" }

    Compress-Archive -Path (Join-Path $temp "*") -DestinationPath $outZip -Force
    Write-Host "Arsip deploy: $outZip"
    Write-Host "Pastikan .env dibuat di server dari .env.example — jangan sertakan .env di ZIP."
}
finally {
    Remove-Item $temp -Recurse -Force -ErrorAction SilentlyContinue
}
