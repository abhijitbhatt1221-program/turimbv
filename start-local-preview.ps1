$ErrorActionPreference = 'Stop'
$projectPath = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location -LiteralPath $projectPath

Write-Host 'TOURIM preview: http://127.0.0.1:7000/'
php -S 127.0.0.1:7000 router.php
