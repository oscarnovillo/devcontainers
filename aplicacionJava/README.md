# 🌤️ Aplicación del Clima - Java Spring Boot

Aplicación del clima desarrollada en Java Spring Boot, equivalente a las versiones de Rust, Python y Node.js. Utiliza la API de WeatherAPI para obtener información meteorológica en tiempo real.

## 🚀 Características

- ✅ **Spring Boot 3.2** con Java 21
- ✅ **WebFlux** para programación reactiva
- ✅ **API REST** con endpoints documentados
- ✅ **Docker** multi-stage para optimización
- ✅ **Health checks** integrados
- ✅ **Tests unitarios** con JUnit 5
- ✅ **Configuración flexible** con variables de entorno
- ✅ **Logging configurable**

## 📋 Requisitos

- Java 21+ (para desarrollo local)
- Maven 3.6+ (para desarrollo local)
- Docker y Docker Compose (para contenedores)
- API Key de [WeatherAPI.com](https://www.weatherapi.com/)

## 🛠️ Instalación y Configuración

### 1. Clonar y configurar

```bash
git clone <repository-url>
cd aplicacionJava
```

### 2. Configurar variables de entorno

```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Editar .env con tu API key
WEATHER_API_KEY=tu_api_key_aqui
DEBUG=true
LOG_LEVEL=debug
```

### 3. Ejecución con Maven (desarrollo)

```bash
# Instalar dependencias y ejecutar tests
mvn clean test

# Ejecutar la aplicación
mvn spring-boot:run
```

### 4. Ejecución con Docker

```bash
# Construir y ejecutar con Docker Compose
docker-compose up --build

# O ejecutar solo el build de Docker
docker build --build-arg WEATHER_API_KEY=tu_api_key -t clima-app-java .
docker run -p 8080:8080 -e WEATHER_API_KEY=tu_api_key clima-app-java
```

## 🌐 Endpoints de la API

### Información del clima

```http
GET /api/clima?ciudad=Madrid
GET /api/clima/Madrid
```

**Respuesta:**
```json
{
  "ciudad": "Madrid",
  "pais": "Spain", 
  "temperatura": 22.0,
  "descripcion": "Partly cloudy",
  "humedad": 60,
  "viento": 10.0,
  "direccionViento": "SW",
  "fechaActualizacion": "2025-05-25 10:30"
}
```

### Health check

```http
GET /api/health
```

**Respuesta:**
```json
{
  "status": "UP",
  "service": "Clima API Java",
  "message": "Servicio funcionando correctamente...",
  "timestamp": 1716631800000
}
```

### Información de la API

```http
GET /api/
```

## ⚙️ Configuración

### Variables de entorno

| Variable | Descripción | Valor por defecto |
|----------|-------------|------------------|
| `WEATHER_API_KEY` | Clave de WeatherAPI | `demo_key` |
| `DEBUG` | Modo debug | `false` |
| `LOG_LEVEL` | Nivel de logging | `info` |
| `SERVER_PORT` | Puerto del servidor | `8080` |
| `JAVA_OPTS` | Opciones de JVM | `-Xmx512m -Xms256m` |

### Perfiles de Spring

- **development**: Para desarrollo local
- **production**: Para producción (Docker)
- **docker**: Para ejecución en contenedores

## 🧪 Testing

```bash
# Ejecutar todos los tests
mvn test

# Ejecutar tests con reporte de cobertura
mvn test jacoco:report

# Ver reporte de cobertura
open target/site/jacoco/index.html
```

### Tests incluidos

- ✅ Test de contexto de Spring Boot
- ✅ Tests del controlador REST
- ✅ Tests de integración con WebFlux
- ✅ Mocking de servicios externos

## 🐳 Docker

### Construcción

```bash
# Build básico
docker build -t clima-app-java .

# Build con API key
docker build --build-arg WEATHER_API_KEY=tu_api_key -t clima-app-java .
```

### Características del Dockerfile

- **Multi-stage build** para optimización
- **Usuario no-root** para seguridad
- **Health check** integrado
- **Imagen mínima** basada en Alpine
- **Variables de entorno** configurables

## 📊 Monitoreo

### Actuator endpoints disponibles

- `/actuator/health` - Estado de la aplicación
- `/actuator/info` - Información de la aplicación
- `/actuator/metrics` - Métricas de rendimiento

### Logs

Los logs incluyen:
- Solicitudes HTTP
- Errores de API externa
- Información de debug (si está habilitado)
- Métricas de rendimiento

## 🔧 Desarrollo

### Estructura del proyecto

```
src/
├── main/java/com/example/clima/
│   ├── ClimaApplication.java          # Clase principal
│   ├── config/                        # Configuración
│   │   ├── WeatherProperties.java     # Propiedades
│   │   └── WebClientConfig.java       # Cliente HTTP
│   ├── controller/                    # Controladores REST
│   │   └── ClimaController.java
│   ├── model/                         # Modelos de datos
│   │   ├── ClimaInfo.java
│   │   └── WeatherResponse.java
│   └── service/                       # Servicios
│       └── ClimaService.java
└── test/java/                         # Tests
```

### Comandos útiles

```bash
# Compilar sin ejecutar tests
mvn compile -DskipTests

# Generar JAR ejecutable
mvn package

# Limpiar y recompilar
mvn clean compile

# Ejecutar con perfil específico
mvn spring-boot:run -Dspring-boot.run.profiles=development
```

## 🚀 GitHub Actions

Esta aplicación incluye un workflow de CI/CD que:

- ✅ Compila con Maven
- ✅ Ejecuta tests
- ✅ Verifica calidad de código
- ✅ Construye imagen Docker
- ✅ Publica a Docker Hub

Ver `.github/workflows/java.yml` para más detalles.

## 🤝 Comparación con otras versiones

| Característica | Java | Rust | Python | Node.js |
|---------------|------|------|--------|---------|
| **Framework** | Spring Boot | Tokio + Reqwest | FastAPI | Express |
| **Concurrencia** | WebFlux (Reactive) | Async/await | AsyncIO | Event Loop |
| **Build time** | ~30s | ~45s | ~5s | ~10s |
| **Imagen Docker** | ~200MB | ~50MB | ~100MB | ~150MB |
| **Startup time** | ~3s | ~0.1s | ~1s | ~0.5s |

## 📝 Notas técnicas

- Utiliza **WebFlux** para manejar solicitudes de forma reactiva
- **Jackson** para serialización/deserialización JSON
- **WebClient** para llamadas HTTP no bloqueantes
- **Spring Boot Actuator** para monitoreo
- **JUnit 5** y **WebFluxTest** para testing

## 🔍 Troubleshooting

### Error común: "API key not found"
```bash
# Verificar que la variable esté definida
echo $WEATHER_API_KEY

# Si no está definida, añadirla al .env
echo "WEATHER_API_KEY=tu_api_key" >> .env
```

### Error: "Port already in use"
```bash
# Cambiar el puerto en application.properties
server.port=8081

# O usar variable de entorno
export SERVER_PORT=8081
```

### Error de compilación Maven
```bash
# Limpiar caché de Maven
mvn clean

# Reinstalar dependencias
mvn dependency:purge-local-repository
```

## 📄 Licencia

Este proyecto es parte del curso de DevContainers y está disponible para uso educativo.
