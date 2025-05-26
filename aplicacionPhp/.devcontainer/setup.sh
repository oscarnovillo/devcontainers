#!/bin/bash

# Script de inicio para dev container - Aplicación PHP Clima

echo "🚀 Iniciando configuración del entorno de desarrollo..."

# Verificar que composer está disponible
if ! command -v composer &> /dev/null; then
    echo "❌ Composer no encontrado"
    exit 1
fi

# Verificar que PHP está disponible
if ! command -v php &> /dev/null; then
    echo "❌ PHP no encontrado"
    exit 1
fi

echo "✅ PHP $(php -v | head -n1 | cut -d' ' -f2) encontrado"
echo "✅ Composer $(composer --version | head -n1 | cut -d' ' -f3) encontrado"

# Verificar extensiones de PHP necesarias
echo "🔍 Verificando extensiones de PHP..."
php -m | grep -E "(curl|json|mbstring|zip|gd|pdo)" || echo "⚠️  Algunas extensiones podrían faltar"

# Instalar dependencias si no existen
if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install
else
    echo "✅ Dependencias ya instaladas"
fi

# Verificar archivo .env
if [ ! -f ".env" ]; then
    echo "⚠️  Archivo .env no encontrado"
    echo "💡 Copia .env.example a .env y configura tu WEATHER_API_KEY"
    cp .env.example .env 2>/dev/null || echo "❌ No se pudo copiar .env.example"
else
    echo "✅ Archivo .env encontrado"
fi

# Verificar permisos de directorios
echo "🔐 Configurando permisos..."
mkdir -p storage/logs
chmod -R 777 storage/ 2>/dev/null || echo "⚠️  No se pudieron configurar permisos para storage/"
mkdir -p .phpunit.cache
chmod -R 777 .phpunit.cache 2>/dev/null || echo "⚠️  No se pudieron configurar permisos para .phpunit.cache"

# Ejecutar tests básicos
echo "🧪 Ejecutando tests básicos..."
if composer test 2>/dev/null; then
    echo "✅ Tests ejecutados correctamente"
else
    echo "⚠️  Los tests no se ejecutaron correctamente (normal en la primera configuración)"
fi

# Verificar configuración de Xdebug
echo "🐛 Verificando configuración de Xdebug..."
if php -m | grep -q xdebug; then
    echo "✅ Xdebug está instalado y disponible"
    echo "   Puerto configurado: 9003"
else
    echo "⚠️  Xdebug no está disponible"
fi

echo ""
echo "🎉 ¡Configuración completada!"
echo ""
echo "📚 Comandos útiles:"
echo "   composer test          - Ejecutar tests"
echo "   composer test-coverage - Tests con coverage"
echo "   composer phpstan       - Análisis estático"
echo "   composer cs-fix        - Formatear código"
echo "   composer dev           - Servidor de desarrollo"
echo ""
echo "🌐 URLs disponibles:"
echo "   http://localhost       - Apache (puerto 80)"
echo "   http://localhost:8080  - PHP dev server"
echo ""
echo "🐛 Debugging:"
echo "   Puerto Xdebug: 9003"
echo "   Usar 'Listen for Xdebug' en VS Code"
echo ""
