# DevContainer para Aplicación de Clima Python

Este directorio contiene la configuración del DevContainer para desarrollar la aplicación de clima en Python usando Visual Studio Code.

## ¿Qué es un DevContainer?

Un DevContainer (Development Container) es un contenedor Docker configurado específicamente para desarrollo que incluye todas las herramientas, extensiones y dependencias necesarias para trabajar en el proyecto.

## Características incluidas

### Herramientas de desarrollo
- Python 3.11
- pip y todas las dependencias del proyecto
- Git
- Curl

### Extensiones de VS Code
- Python (Microsoft)
- Black Formatter
- Flake8 Linter
- Pylint
- isort
- Docker
- GitHub Copilot (si está disponible)

### Herramientas adicionales
- Black (formateador de código)
- Flake8 (linter)
- Pylint (análisis estático)
- isort (organización de imports)
- pytest (testing)
- IPython (shell interactivo mejorado)
- Jupyter (notebooks)

## Cómo usar

1. **Prerrequisitos:**
   - Tener instalado VS Code
   - Tener instalada la extensión "Dev Containers" en VS Code
   - Tener Docker Desktop en funcionamiento

2. **Abrir en DevContainer:**
   - Abre la carpeta `aplicacionPython` en VS Code
   - VS Code detectará la configuración del DevContainer
   - Aparecerá una notificación para "Reopen in Container"
   - O usa el Command Palette (Ctrl+Shift+P) y busca "Dev Containers: Reopen in Container"

3. **Primera vez:**
   - El contenedor se construirá automáticamente
   - Se instalarán todas las dependencias
   - Se configurarán las extensiones

4. **Configurar variables de entorno:**
   - Crea un archivo `.env` en la raíz del proyecto con tu API key:
     ```
     WEATHER_API_KEY=tu_clave_api_aqui
     DEBUG=true
     LOG_LEVEL=debug
     PYTHON_ENV=development
     ```

## Configuración incluida

### Formateo automático
- Black configurado para formatear automáticamente al guardar
- Línea máxima de 88 caracteres
- isort para organizar imports automáticamente

### Linting
- Flake8 para detección de errores de estilo
- Pylint para análisis estático avanzado
- Configuración en `pyproject.toml`

### Depuración
- Configuración de depuración lista para usar
- Variables de entorno de desarrollo preconfiguradas

## Estructura del DevContainer

```
.devcontainer/
├── devcontainer.json    # Configuración principal
└── Dockerfile          # Imagen personalizada para desarrollo
```

## Comandos útiles en el contenedor

```bash
# Ejecutar la aplicación
python app.py

# Formatear código
black .

# Verificar formato
black --check .

# Ejecutar linting
flake8 .
pylint app.py

# Organizar imports
isort .

# Shell interactivo
ipython
```

## Ventajas del DevContainer

- **Entorno consistente:** Todos los desarrolladores trabajan con el mismo entorno
- **Configuración automática:** No necesitas instalar Python, dependencias o extensiones manualmente
- **Aislamiento:** No afecta tu sistema local
- **Portabilidad:** Funciona igual en cualquier máquina con Docker
- **Productividad:** Todas las herramientas están preconfiguradas y listas para usar
