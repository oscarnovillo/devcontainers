# Monorepo de Aplicaciones de Clima Multi-Lenguaje

Este repositorio contiene cinco aplicaciones de clima implementadas en diferentes lenguajes de programación, cada una utilizando la misma API externa (WeatherAPI.com) para demostrar diferentes enfoques tecnológicos.

## 🌤️ Aplicaciones Disponibles

### 🦀 [Rust - aplicacionRust/](./applicacionRust/)
- **Framework**: Rust con reqwest y serde
- **Características**: Alto rendimiento, memoria segura, binario compilado
- **Puerto**: 8080
- **Docker**: `clima-app-rust:latest`

### 🐍 [Python - aplicacionPython/](./aplicacionPython/)
- **Framework**: Flask con requests
- **Características**: Sintaxis simple, desarrollo rápido, amplio ecosistema
- **Puerto**: 5000
- **Docker**: `clima-app-python:latest`

### 🟢 [Node.js - aplicacionNode/](./aplicacionNode/)
- **Framework**: Express.js con axios
- **Características**: JavaScript full-stack, NPM ecosystem, async por defecto
- **Puerto**: 3000
- **Docker**: `clima-app-node:latest`

### ☕ [Java - aplicacionJava/](./aplicacionJava/)
- **Framework**: Spring Boot con WebFlux (reactivo)
- **Características**: Enterprise-grade, tipado fuerte, ecosistema maduro
- **Puerto**: 8080
- **Docker**: `clima-app-java:latest`

### 🐘 [PHP - aplicacionPhp/](./aplicacionPhp/)
- **Framework**: PHP nativo con Guzzle HTTP client
- **Características**: Web-first, fácil deployment, composer ecosystem
- **Puerto**: 8080
- **Docker**: `clima-app-php:latest`

## 🚀 Inicio Rápido

### Requisitos
- Docker y Docker Compose
- Clave de API de [WeatherAPI.com](https://www.weatherapi.com/) (gratuita)

### Ejecutar cualquier aplicación

```bash
# 1. Clonar el repositorio
git clone <repo-url>
cd practicas_devcontainers

# 2. Elegir una aplicación (ejemplo: Python)
cd aplicacionPython

# 3. Configurar variables de entorno
cp .env.example .env
# Editar .env y agregar tu WEATHER_API_KEY

# 4. Ejecutar con Docker Compose
docker-compose up --build
```

### Probar la API

```bash
# Obtener clima de Madrid
curl http://localhost:<puerto>/api/clima/Madrid

# Respuesta esperada:
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

## 🏗️ Arquitectura del Monorepo

```
practicas_devcontainers/
├── .github/workflows/          # CI/CD para todas las aplicaciones
│   ├── monorepo.yml           # Coordinador principal
│   ├── rust.yml               # Pipeline Rust
│   ├── python.yml             # Pipeline Python
│   ├── node.yml               # Pipeline Node.js
│   ├── java.yml               # Pipeline Java
│   └── php.yml                # Pipeline PHP
├── applicacionRust/           # App Rust con Cargo
├── aplicacionPython/          # App Python con pip/poetry
├── aplicacionNode/            # App Node.js con npm
├── aplicacionJava/            # App Java con Maven
├── aplicacionPhp/             # App PHP con Composer
└── GITHUB_ACTIONS.md          # Documentación de CI/CD
```

## 🔄 CI/CD Automatizado

Cada aplicación tiene su propio pipeline de GitHub Actions que incluye:

- ✅ **Linting y formato** de código
- ✅ **Tests automatizados**
- ✅ **Build y empaquetado**
- ✅ **Construcción de imagen Docker**
- ✅ **Publicación a Docker Hub** (en main)

### Triggers Inteligentes
Los pipelines solo se ejecutan cuando hay cambios en la aplicación correspondiente, optimizando recursos y tiempo.

## 🌐 API Común

Todas las aplicaciones exponen la misma API REST:

### Endpoints
- `GET /` - Página web interactiva
- `GET /api/health` - Estado de la aplicación
- `GET /api/clima/{ciudad}` - Información del clima
- `POST /api/clima` - Información del clima (JSON body)

### Formato de Respuesta
```json
{
  "ciudad": "Barcelona",
  "region": "Catalonia",
  "pais": "Spain",
  "temperatura": 20.5,
  "descripcion": "Parcialmente nublado",
  "humedad": 65,
  "sensacion_termica": 22.1,
  "timestamp": "2024-01-15T14:30:00+00:00"
}
```

## 🐳 Docker y Contenedores

### Imágenes Optimizadas
Cada aplicación incluye:
- **Multi-stage builds** para tamaño mínimo
- **Usuarios no-root** para seguridad
- **Health checks** incorporados
- **Variables de entorno** configurables

### Ejecutar con Docker
```bash
# Ejecutar aplicación específica
docker run -p 8080:8080 -e WEATHER_API_KEY=tu_clave clima-app-rust:latest

# O usar docker-compose para desarrollo
cd aplicacionPython && docker-compose up
```

## 📊 Comparación de Tecnologías

| Aspecto | Rust | Python | Node.js | Java | PHP |
|---------|------|--------|---------|------|-----|
| **Performance** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Desarrollo** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Ecosistema** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| **Memoria** | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐ |
| **Deploy** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |

## 🛠️ Desarrollo Local

### Configurar entorno de desarrollo:

1. **Clonar el repositorio**
2. **Elegir aplicación** de interés
3. **Seguir README específico** de la aplicación
4. **Configurar variables de entorno**
5. **Instalar dependencias** del lenguaje
6. **Ejecutar en modo desarrollo**

### Herramientas recomendadas:
- **VS Code** con extensiones para cada lenguaje
- **Docker Desktop** para contenedores
- **Postman/curl** para testing de APIs
- **Git** para control de versiones

## 🔧 Configuración de Variables

### Variables requeridas para todas las aplicaciones:
```bash
WEATHER_API_KEY=tu_clave_de_weatherapi_com
```

### Variables opcionales:
```bash
DEBUG=true                     # Habilitar logs de debug
LOG_LEVEL=info                # Nivel de logging
API_BASE_URL=https://api.weatherapi.com/v1  # URL de la API
```

## 📚 Documentación Adicional

- **[GitHub Actions](./GITHUB_ACTIONS.md)** - Documentación completa de CI/CD
- **[Aplicación Rust](./applicacionRust/README.md)** - Detalles específicos de Rust
- **[Aplicación Python](./aplicacionPython/README.md)** - Detalles específicos de Python
- **[Aplicación Node.js](./aplicacionNode/README.md)** - Detalles específicos de Node.js
- **[Aplicación Java](./aplicacionJava/README.md)** - Detalles específicos de Java
- **[Aplicación PHP](./aplicacionPhp/README.md)** - Detalles específicos de PHP

## 🤝 Contribución

1. **Fork** el repositorio
2. **Crear rama** para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. **Commit** tus cambios (`git commit -m 'Añadir nueva funcionalidad'`)
4. **Push** a la rama (`git push origin feature/nueva-funcionalidad`)
5. **Crear Pull Request**

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivos individuales para más detalles.

## 🌟 Características Destacadas

- ✅ **5 lenguajes** diferentes, mismo objetivo
- ✅ **API consistente** entre todas las implementaciones
- ✅ **CI/CD automatizado** con GitHub Actions
- ✅ **Docker optimizado** para cada tecnología
- ✅ **Documentación completa** para cada aplicación
- ✅ **Configuración flexible** con variables de entorno
- ✅ **Monorepo inteligente** con triggers condicionales
- ✅ **Testing automatizado** en múltiples versiones
- ✅ **Linting y formato** automático de código

Este proyecto sirve como ejemplo práctico de cómo implementar la misma funcionalidad en múltiples tecnologías, manteniendo buenas prácticas de desarrollo y DevOps.
