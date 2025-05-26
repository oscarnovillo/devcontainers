# Aplicación de Clima en Node.js

Una aplicación CLI simple desarrollada en Node.js que consulta la API de WeatherAPI para mostrar información meteorológica de cualquier ciudad.

## Requisitos

- Node.js 18+
- npm
- Cuenta en [WeatherAPI](https://www.weatherapi.com/) para obtener una clave API

## Configuración

1. Crea un archivo `.env` en la raíz del proyecto con el siguiente contenido:
   ```
   WEATHER_API_KEY=tu_clave_api_de_weatherapi
   DEBUG=false
   LOG_LEVEL=info
   NODE_ENV=development
   ```

## Desarrollo con DevContainer (Recomendado)

Para una experiencia de desarrollo optimizada, puedes usar el DevContainer incluido:

1. Abre este proyecto en VS Code
2. Instala la extensión "Dev Containers" si no la tienes
3. Cuando VS Code detecte el DevContainer, haz clic en "Reopen in Container"
4. El entorno se configurará automáticamente con todas las herramientas necesarias

Ver más detalles en [.devcontainer/README.md](.devcontainer/README.md)

## Instalación local

```bash
npm install
```

## Ejecución local

```bash
# Modo normal
npm start

# Modo desarrollo
npm run dev

# Modo producción
npm run prod
```

## Docker

### Construcción manual

```bash
# Para producción
docker build -t tu-usuario/clima_app_node .
docker run -it --env-file .env tu-usuario/clima_app_node

# Para desarrollo
docker build -f Dockerfile.dev -t tu-usuario/clima_app_node:dev .
docker run -it --env-file .env tu-usuario/clima_app_node:dev
```

### Variables de entorno

- `WEATHER_API_KEY`: Tu clave API de WeatherAPI (requerida)
- `DEBUG`: Habilita/deshabilita el modo depuración (true/false)
- `LOG_LEVEL`: Nivel de logging (info, debug, warning, error)
- `NODE_ENV`: Entorno de ejecución (development/production)

### Despliegue automático con GitHub Actions

Este proyecto incluye un workflow de GitHub Actions que:
- Ejecuta en múltiples versiones de Node.js (18.x, 20.x)
- Ejecuta linting con ESLint
- Verifica formato de código con Prettier
- Ejecuta tests básicos
- Construye y publica la imagen Docker en Docker Hub

Para configurar el despliegue automático:

1. Haz fork de este repositorio
2. En tu fork, ve a Settings > Secrets and variables > Actions
3. Añade los siguientes secretos:
   - `DOCKERHUB_USERNAME`: Tu nombre de usuario de Docker Hub
   - `DOCKERHUB_TOKEN`: Un token de acceso de Docker Hub (puedes generarlo en Account Settings > Security > New Access Token)
4. Haz push a la rama principal o crea un tag con el formato `v*` (ej. v1.0.0)

La GitHub Action construirá la imagen Docker y la publicará en Docker Hub con el nombre `tu-usuario/clima-app-node:latest`.

Ver más detalles en [.github/README.md](.github/README.md)

## Funcionalidades

- Consulta información meteorológica en tiempo real
- Soporte para múltiples modos de entorno (desarrollo/producción)
- Modo de depuración configurable
- Manejo de errores robusto
- Interfaz CLI interactiva
- Optimizado para contenedores Docker
- Manejo graceful de señales de sistema

## Scripts disponibles

- `npm start`: Ejecuta la aplicación en modo normal
- `npm run dev`: Ejecuta la aplicación en modo desarrollo
- `npm run prod`: Ejecuta la aplicación en modo producción

## Dependencias

- **axios**: Cliente HTTP para realizar peticiones a la API
- **dotenv**: Carga variables de entorno desde archivos .env

## Dependencias de desarrollo

- **nodemon**: Reinicia automáticamente la aplicación durante el desarrollo
