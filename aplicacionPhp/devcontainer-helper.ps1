# Script de ayuda para DevContainer - Aplicación PHP Clima
# Para usar: .\devcontainer-helper.ps1

Write-Host "🐳 DevContainer Helper - Aplicación PHP Clima" -ForegroundColor Cyan
Write-Host ""

# Verificar si Docker está corriendo
$dockerRunning = docker info 2>$null
if (-not $dockerRunning) {
    Write-Host "❌ Docker no está ejecutándose. Por favor, inicia Docker Desktop." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Docker está ejecutándose" -ForegroundColor Green

# Verificar si VS Code está instalado
$vscodeInstalled = Get-Command code -ErrorAction SilentlyContinue
if (-not $vscodeInstalled) {
    Write-Host "❌ VS Code no está instalado o no está en PATH" -ForegroundColor Red
    Write-Host "   Descarga VS Code desde: https://code.visualstudio.com/" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ VS Code está instalado" -ForegroundColor Green

# Verificar si la extensión Dev Containers está instalada
$devContainerExt = code --list-extensions | Select-String "ms-vscode-remote.remote-containers"
if (-not $devContainerExt) {
    Write-Host "⚠️  La extensión 'Dev Containers' no está instalada" -ForegroundColor Yellow
    Write-Host "   Instalando extensión..." -ForegroundColor Yellow
    code --install-extension ms-vscode-remote.remote-containers
    Start-Sleep 3
} else {
    Write-Host "✅ Extensión 'Dev Containers' está instalada" -ForegroundColor Green
}

Write-Host ""
Write-Host "🚀 Para comenzar a desarrollar:" -ForegroundColor Cyan
Write-Host "   1. Abre VS Code en la carpeta aplicacionPhp:" -ForegroundColor White
Write-Host "      code ." -ForegroundColor Gray
Write-Host "   2. Cuando aparezca la notificación, selecciona 'Reopen in Container'" -ForegroundColor White
Write-Host "   3. O usa Ctrl+Shift+P y busca 'Dev Containers: Reopen in Container'" -ForegroundColor White
Write-Host ""
Write-Host "📁 Estructura del DevContainer creada:" -ForegroundColor Cyan
Write-Host "   .devcontainer/" -ForegroundColor Gray
Write-Host "   ├── devcontainer.json    # Configuración principal" -ForegroundColor Gray
Write-Host "   ├── Dockerfile          # Imagen personalizada" -ForegroundColor Gray
Write-Host "   ├── README.md           # Documentación completa" -ForegroundColor Gray
Write-Host "   ├── setup.sh            # Script de configuración" -ForegroundColor Gray
Write-Host "   └── xdebug.ini          # Configuración de Xdebug" -ForegroundColor Gray
Write-Host ""
Write-Host "   .vscode/" -ForegroundColor Gray
Write-Host "   ├── launch.json         # Configuración de debugging" -ForegroundColor Gray
Write-Host "   ├── settings.json       # Configuración del editor" -ForegroundColor Gray
Write-Host "   └── tasks.json          # Tareas automatizadas" -ForegroundColor Gray
Write-Host ""
Write-Host "💡 Características incluidas:" -ForegroundColor Cyan
Write-Host "   • PHP 8.1 + Apache configurado" -ForegroundColor White
Write-Host "   • Xdebug para debugging" -ForegroundColor White
Write-Host "   • PHPUnit para testing" -ForegroundColor White
Write-Host "   • PHPStan para análisis estático" -ForegroundColor White
Write-Host "   • PHP-CS-Fixer para formateo" -ForegroundColor White
Write-Host "   • Todas las extensiones de VS Code necesarias" -ForegroundColor White
Write-Host "   • Configuración automática completa" -ForegroundColor White
Write-Host ""

# Preguntar si quiere abrir VS Code ahora
$response = Read-Host "¿Quieres abrir VS Code en esta carpeta ahora? (s/n)"
if ($response -eq 's' -or $response -eq 'S' -or $response -eq 'y' -or $response -eq 'Y') {
    Write-Host "🚀 Abriendo VS Code..." -ForegroundColor Green
    code .
}
