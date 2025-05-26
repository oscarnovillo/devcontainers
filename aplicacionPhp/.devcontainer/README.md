# DevContainer para Aplicación de Clima PHP

Este directorio contiene la configuración del DevContainer para desarrollar la aplicación de clima en PHP usando Visual Studio Code.

## ¿Qué es un DevContainer?

Un DevContainer (Development Container) es un contenedor Docker configurado específicamente para desarrollo que incluye todas las herramientas, extensiones y dependencias necesarias para trabajar en el proyecto.

## Características incluidas

### Entorno PHP
- PHP 8.1 con Apache
- Extensiones PHP necesarias (gd, zip, pdo, mbstring, etc.)
- Composer preinstalado
- Xdebug configurado para debugging y coverage

### Herramientas de desarrollo
- Git y GitHub CLI
- Todas las dependencias del proyecto instaladas
- PHPUnit para testing
- PHPStan para análisis estático  
- PHP-CS-Fixer para formateo de código
- PHP CodeSniffer para linting

### Extensiones de VS Code
- **Intelephense**: IntelliSense avanzado para PHP
- **PHP Debug**: Debugging con Xdebug
- **PHPUnit**: Ejecución de tests integrada
- **PHPStan**: Análisis estático en tiempo real
- **PHP CS Fixer**: Formateo automático
- **Docker**: Soporte para contenedores
- **GitHub Copilot**: Asistente de IA (si está disponible)

### Configuraciones incluidas
- Formateo automático al guardar
- Fix automático de errores de linting
- Debugging configurado en puerto 9003
- Variables de entorno de desarrollo preconfiguradas
- Apache configurado con mod_rewrite

## Cómo usar

1. **Prerrequisitos:**
   - Tener instalado VS Code
   - Tener instalada la extensión "Dev Containers" en VS Code
   - Tener Docker Desktop en funcionamiento

2. **Abrir en DevContainer:**
   - Abre la carpeta `aplicacionPhp` en VS Code
   - VS Code detectará la configuración del DevContainer
   - Aparecerá una notificación para "Reopen in Container"
   - O usa el Command Palette (Ctrl+Shift+P) y busca "Dev Containers: Reopen in Container"

3. **Primera vez:**
   - El contenedor se construirá automáticamente
   - Se instalarán todas las dependencias con `composer install`
   - Se configurarán las extensiones y permisos

4. **Configurar variables de entorno:**
   - Crea un archivo `.env` en la raíz del proyecto con tu API key:
     ```
     WEATHER_API_KEY=tu_clave_api_aquí
     APP_ENV=development
     DEBUG=true
     LOG_LEVEL=debug
     ```

## Configuración incluida

### Formateo automático
- PHP-CS-Fixer configurado para formatear automáticamente al guardar
- Reglas PSR-12 y mejores prácticas
- Configuración en `.php-cs-fixer.php`

### Linting y análisis estático
- PHPStan configurado con nivel 8 (máximo)
- PHP CodeSniffer con estándar PSR-12
- Configuraciones en `phpstan.neon`

### Testing
- PHPUnit configurado para tests unitarios e integración
- Coverage de código disponible
- Configuración en `phpunit.xml`

### Debugging
- Xdebug configurado y listo para usar
- Puerto 9003 configurado para debugging
- Variables de entorno de desarrollo preconfiguradas

## Estructura del DevContainer

```
.devcontainer/
├── devcontainer.json    # Configuración principal
├── Dockerfile          # Imagen personalizada para desarrollo
└── README.md           # Esta documentación
```

## Scripts disponibles

Una vez dentro del contenedor, puedes usar estos comandos:

```bash
# Instalar dependencias
composer install

# Ejecutar tests
composer test

# Ejecutar tests con coverage
composer test-coverage

# Análisis estático con PHPStan
composer phpstan

# Verificar estilo de código
composer cs-check

# Corregir estilo de código
composer cs-fix

# Ejecutar todos los linters
composer lint

# Formatear código
composer format

# Iniciar servidor de desarrollo
composer dev
```

## Comandos útiles en el contenedor

```bash
# Ver logs de Apache
tail -f /var/log/apache2/error.log

# Ver logs de Xdebug
tail -f /tmp/xdebug.log

# Reiniciar Apache
sudo service apache2 restart

# Ver estado de servicios
sudo service --status-all

# Verificar configuración de PHP
php -i | grep xdebug
```

## Configuración de VS Code

### Debugging
- Configuración de launch.json ya preparada
- Breakpoints y debugging completo disponible
- Variables de entorno de desarrollo preconfiguradas

### IntelliSense
- Intelephense configurado para máximo rendimiento
- Autocompletado avanzado
- Navegación de código mejorada

### Formateo automático
- PHP-CS-Fixer como formateador por defecto
- Formateo automático al guardar
- Fix automático de linting al guardar

## Puertos Forward

- **Puerto 80:** Apache server (aplicación web)
- **Puerto 8080:** PHP development server alternativo
- **Puerto 9003:** Xdebug (debugging)

Estos puertos se abren automáticamente cuando el contenedor detecta servicios corriendo en ellos.

## Ventajas del DevContainer

- **Entorno consistente:** Todos los desarrolladores trabajan con el mismo entorno
- **Configuración automática:** No necesitas instalar PHP, Apache, extensiones o herramientas manualmente
- **Aislamiento:** No afecta tu sistema local
- **Portabilidad:** Funciona igual en cualquier máquina con Docker
- **Productividad:** Todas las herramientas están preconfiguradas y listas para usar
- **Debugging:** Xdebug configurado y funcionando desde el primer momento
- **Testing:** PHPUnit y cobertura de código listos para usar
- **Calidad de código:** Linting y formateo automático configurado

## Troubleshooting

### Problema: Xdebug no funciona
- Verifica que el puerto 9003 esté disponible en tu máquina local
- Revisa la configuración en VS Code (launch.json)
- Comprueba los logs: `tail -f /tmp/xdebug.log`

### Problema: Permisos de archivos
```bash
# Desde dentro del contenedor
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chmod -R 777 storage
```

### Problema: Composer muy lento
- El primer `composer install` puede tardar unos minutos
- Las siguientes ejecuciones serán más rápidas gracias al cache

### Problema: Apache no inicia
- Revisa los logs: `tail -f /var/log/apache2/error.log`
- Verifica la configuración: `apache2ctl configtest`
- Reinicia el servicio: `sudo service apache2 restart`
