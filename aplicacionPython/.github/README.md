# GitHub Actions Workflow para Aplicación Python

Este directorio contiene el workflow de GitHub Actions para automatizar el CI/CD de la aplicación de clima en Python.

## 🚀 Características del Workflow

### Triggers (Disparadores)
- **Push** a la rama `main`
- **Pull Requests** hacia la rama `main`
- **Tags** con formato `v*` (ej: v1.0.0, v1.2.3)

### Jobs y Pasos

#### 1. **Setup del entorno**
- Checkout del código
- Configuración de Python 3.11
- Cache de dependencias pip

#### 2. **Instalación de dependencias**
- Upgrade de pip
- Instalación desde `requirements.txt`
- Instalación de herramientas de desarrollo (black, flake8, pylint, pytest)

#### 3. **Linting y formato**
- Linting con **Flake8** (errores de sintaxis y estilo)
- Análisis estático con **Pylint**
- Verificación de formato con **Black**

#### 4. **Testing**
- Ejecución de tests con **pytest**
- Test básico de importación

#### 5. **Docker Build & Push**
- Setup de Docker Buildx
- Login a Docker Hub
- Build y push de la imagen Docker
- Versionado automático
- Optimización con cache

## ⚙️ Configuración Requerida

### Secretos de GitHub (GitHub Secrets)

Necesitas configurar estos secretos en tu repositorio:

1. Ve a **Settings > Secrets and variables > Actions**
2. Añade los siguientes secretos:

| Secreto | Descripción |
|---------|------------|
| `DOCKERHUB_USERNAME` | Tu nombre de usuario de Docker Hub |
| `DOCKERHUB_TOKEN` | Token de acceso de Docker Hub |

### Variables de GitHub (Opcional)

Puedes configurar estas variables para el versionado:

| Variable | Descripción | Valor por defecto |
|----------|------------|------------------|
| `MAJOR_VERSION` | Versión mayor | 1 |
| `MINOR_VERSION` | Versión menor | 0 |

## 📦 Imágenes Docker Generadas

El workflow genera estas imágenes:

```
tu-usuario/clima-app-python:latest
tu-usuario/clima-app-python:1.0.{run_number}
```

## 🧪 Testing y Calidad de Código

### Herramientas incluidas:

#### **Flake8** - Linting
- Detecta errores de sintaxis
- Verifica estilo PEP 8
- Configurable en `.flake8` o `pyproject.toml`

#### **Pylint** - Análisis estático
- Análisis profundo del código
- Detección de errores potenciales
- Sugerencias de mejora

#### **Black** - Formateo automático
- Formateo consistente del código
- Línea máxima de 88 caracteres
- Configurable en `pyproject.toml`

#### **pytest** - Testing
- Framework de testing moderno
- Autodescubrimiento de tests
- Reportes detallados

## 🔧 Personalización

### Añadir más tests
Crea archivos de test en la carpeta `tests/`:

```python
# tests/test_app.py
import pytest
from app import obtener_clima, mostrar_clima

def test_obtener_clima():
    # Tu test aquí
    pass
```

### Configurar herramientas en pyproject.toml
```toml
[tool.black]
line-length = 88
target-version = ['py311']

[tool.pylint]
max-line-length = 88

[tool.pytest.ini_options]
testpaths = ["tests"]
python_files = ["test_*.py"]
```

### Cambiar versión de Python
```yaml
- name: Set up Python 3.12
  uses: actions/setup-python@v4
  with:
    python-version: '3.12'
```

## 🐛 Solución de Problemas

### Error: "Flake8 encuentra errores"
```bash
# Ejecutar localmente para ver errores
cd aplicacionPython
flake8 .

# Autofix algunos problemas
autopep8 --in-place --aggressive --aggressive *.py
```

### Error: "Black encuentra diferencias de formato"
```bash
# Formatear código localmente
cd aplicacionPython
black .
```

### Error: "Pylint da puntuación baja"
```bash
# Ver reporte completo
cd aplicacionPython
pylint app.py
```

### Error: "Tests fallan"
```bash
# Ejecutar tests localmente
cd aplicacionPython
pytest -v
```

## 📊 Ejemplo de Ejecución

```
✅ Checkout code
✅ Setup Python 3.11
✅ Install dependencies
✅ Lint with flake8
✅ Lint with pylint
✅ Check formatting with black
✅ Run tests with pytest
✅ Setup Docker Buildx
✅ Login to Docker Hub
✅ Build and push Docker image
   📦 usuario/clima-app-python:latest
   📦 usuario/clima-app-python:1.0.42
```

## 🔍 Métricas de Calidad

El workflow verifica:
- ✅ **Sintaxis correcta** (Flake8)
- ✅ **Estilo PEP 8** (Flake8)
- ✅ **Formato consistente** (Black)
- ✅ **Calidad de código** (Pylint)
- ✅ **Tests pasan** (pytest)
- ✅ **Importaciones funcionan**

## 🚀 Activación del Workflow

El workflow se ejecuta automáticamente cuando:

1. **Haces push** a la rama main
2. **Abres un Pull Request** hacia main
3. **Creas un tag** con formato `v*`

Para activar manualmente:
- Ve a **Actions** en GitHub
- Selecciona el workflow "Python CI with pip and Docker"
- Click en **Run workflow**
