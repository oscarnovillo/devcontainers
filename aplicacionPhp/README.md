# Aplicación de Clima - PHP

Una aplicación web en PHP que proporciona información meteorológica utilizando la API de WeatherAPI.

## Características

- 🌤️ Información meteorológica en tiempo real
- 🏙️ Búsqueda por ciudad
- 🌡️ Datos de temperatura, humedad y sensación térmica
- 📱 API REST con respuestas JSON
- 🐳 Soporte para Docker
- ✅ Pruebas unitarias con PHPUnit
- 📊 Análisis estático con PHPStan
- 🎨 Formateo de código con PHP-CS-Fixer

## Requisitos

- PHP 8.1 o superior
- Composer
- Clave de API de [WeatherAPI.com](https://www.weatherapi.com/)

## Instalación

### Instalación Local

1. **Clonar el repositorio**
   ```bash
   cd aplicacionPhp
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   ```
   
   Editar `.env` y agregar tu clave de API:
   ```
   WEATHER_API_KEY=tu_clave_api_aquí
   PHP_ENV=development
   DEBUG=true
   LOG_LEVEL=info
   ```

4. **Ejecutar la aplicación**
   ```bash
   composer run dev
   ```
   
   La aplicación estará disponible en `http://localhost:8080`

### Instalación con DevContainer (Recomendado para desarrollo)

La forma más fácil de desarrollar esta aplicación es usando el DevContainer incluido:

1. **Prerrequisitos:**
   - VS Code con la extensión "Dev Containers"
   - Docker Desktop en funcionamiento

2. **Abrir en DevContainer:**
   - Abre la carpeta `aplicacionPhp` en VS Code
   - Selecciona "Reopen in Container" cuando aparezca la notificación
   - El entorno se configurará automáticamente con todas las herramientas

3. **Configurar variables de entorno:**
   ```bash
   cp .env.example .env
   ```
   Edita `.env` con tu clave de API de WeatherAPI

4. **¡Listo para desarrollar!**
   - Debugging con Xdebug configurado
   - Tests unitarios listos
   - Linting y formateo automático
   - Todas las extensiones de VS Code preinstaladas

Ver más detalles en [.devcontainer/README.md](.devcontainer/README.md)

### Instalación con Docker

1. **Construir la imagen**
   ```bash
   docker build -t clima-app-php .
   ```

2. **Ejecutar el contenedor**
   ```bash
   docker run -p 8080:8080 -e WEATHER_API_KEY=tu_clave_api clima-app-php
   ```

3. **Usar Docker Compose**
   ```bash
   docker-compose up
   ```

## Uso de la API

### Endpoints Disponibles

#### GET `/api/health`
Verificar el estado de la aplicación.

**Respuesta:**
```json
{
    "status": "ok",
    "timestamp": "2024-01-15T10:30:00+00:00",
    "version": "1.0.0"
}
```

#### GET `/api/clima/{ciudad}`
Obtener información meteorológica de una ciudad.

**Parámetros:**
- `ciudad` (string): Nombre de la ciudad

**Ejemplo:**
```bash
curl http://localhost:8080/api/clima/Madrid
```

**Respuesta:**
```json
{
    "ciudad": "Madrid",
    "region": "Madrid",
    "pais": "Spain",
    "temperatura": 22.5,
    "descripcion": "Soleado",
    "humedad": 45,
    "sensacion_termica": 24.2,
    "timestamp": "2024-01-15T10:30:00+00:00"
}
```

#### POST `/api/clima`
Obtener información meteorológica enviando la ciudad en el cuerpo de la petición.

**Cuerpo de la petición:**
```json
{
    "ciudad": "Barcelona"
}
```

**Respuesta:** Igual que el endpoint GET.

### Página Web

Accede a `http://localhost:8080` para utilizar la interfaz web interactiva.

## Desarrollo

### Comandos Disponibles

```bash
# Instalar dependencias
composer install

# Ejecutar pruebas
composer run test

# Ejecutar pruebas con cobertura
composer run test-coverage

# Análisis estático
composer run phpstan

# Verificar estilo de código
composer run cs-check

# Formatear código
composer run cs-fix

# Ejecutar todos los linters
composer run lint

# Servidor de desarrollo
composer run dev

# Servidor para producción
composer run start
```

### Estructura del Proyecto

```
aplicacionPhp/
├── src/
│   ├── Controller/
│   │   └── ClimaController.php
│   ├── Service/
│   │   └── ClimaService.php
│   ├── Model/
│   │   ├── WeatherResponse.php
│   │   └── ClimaInfo.php
│   ├── Config/
│   │   └── AppConfig.php
│   └── Utils/
│       ├── Logger.php
│       └── Response.php
├── public/
│   ├── index.php
│   ├── api.php
│   ├── style.css
│   └── script.js
├── tests/
│   ├── Unit/
│   │   ├── ClimaServiceTest.php
│   │   └── ClimaControllerTest.php
│   └── bootstrap.php
├── docker/
│   └── php.ini
├── .env.example
├── .gitignore
├── composer.json
├── phpunit.xml
├── phpstan.neon
├── .php-cs-fixer.php
├── Dockerfile
├── docker-compose.yml
└── README.md
```

### Variables de Entorno

| Variable | Descripción | Valor por defecto |
|----------|-------------|-------------------|
| `WEATHER_API_KEY` | Clave de API de WeatherAPI.com | - |
| `PHP_ENV` | Entorno de ejecución | `development` |
| `DEBUG` | Habilitar modo debug | `false` |
| `LOG_LEVEL` | Nivel de logging | `info` |
| `API_BASE_URL` | URL base de WeatherAPI | `https://api.weatherapi.com/v1` |

## Pruebas

```bash
# Ejecutar todas las pruebas
composer run test

# Pruebas con cobertura
composer run test-coverage

# Ver reporte de cobertura
open coverage/index.html
```

## Docker

### Dockerfile Multi-stage

El `Dockerfile` utiliza una construcción multi-stage para optimizar el tamaño de la imagen:

1. **Stage 1 (composer)**: Instala dependencias con Composer
2. **Stage 2 (production)**: Imagen final optimizada solo con archivos necesarios

### Docker Compose

El archivo `docker-compose.yml` incluye:
- Servicio PHP con configuración de variables de entorno
- Volúmenes para desarrollo
- Puerto mapping

## CI/CD

Este proyecto incluye configuración para GitHub Actions con:
- Pruebas automatizadas en múltiples versiones de PHP (8.1, 8.2, 8.3)
- Análisis estático con PHPStan
- Verificación de estilo de código
- Construcción y publicación de imagen Docker

## Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## API Externa

Este proyecto utiliza [WeatherAPI.com](https://www.weatherapi.com/) para obtener datos meteorológicos. Necesitarás registrarte para obtener una clave de API gratuita.
