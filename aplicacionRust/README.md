# Aplicación de Clima en Rust


Una aplicación CLI simple desarrollada en Rust que consulta la API de WeatherAPI para mostrar información meteorológica de cualquier ciudad.

## Requisitos

- Rust
- Cuenta en [WeatherAPI](https://www.weatherapi.com/) para obtener una clave API

## Configuración

1. Crea un archivo `.env` en la raíz del proyecto con el siguiente contenido:
   ```
   WEATHER_API_KEY=tu_clave_api_de_weatherapi
   ```

## Ejecución local

```bash
cargo run
```

## Docker

### Construcción manual

```bash
docker build -t tu-usuario/clima_app .
docker run -it tu-usuario/clima_app
```

### Despliegue automático con GitHub Actions

Este proyecto incluye un workflow de GitHub Actions que:
- Construye y prueba automáticamente el código
- Ejecuta linting con Clippy y verificación de formato
- Construye y publica la imagen Docker en Docker Hub

Para configurar el despliegue automático:

1. Haz fork de este repositorio
2. En tu fork, ve a Settings > Secrets and variables > Actions
3. Añade los siguientes secretos:
   - `DOCKERHUB_USERNAME`: Tu nombre de usuario de Docker Hub
   - `DOCKERHUB_TOKEN`: Un token de acceso de Docker Hub (puedes generarlo en Account Settings > Security > New Access Token)
4. Haz push a la rama principal o crea un tag con el formato `v*` (ej. v1.0.0)

La GitHub Action construirá la imagen Docker y la publicará en Docker Hub con el nombre `tu-usuario/clima-app-rust:latest`.

Ver más detalles en [.github/README.md](../.github/README.md)