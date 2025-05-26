# GitHub Actions para Monorepo Multi-Lenguaje

Este repositorio contiene cuatro aplicaciones en diferentes lenguajes de programación, cada una con su propio workflow de CI/CD optimizado.

## 📁 Estructura del Monorepo

```
.github/workflows/
├── monorepo.yml          # Workflow general de coordinación
├── rust.yml              # CI/CD para aplicación Rust
├── python.yml            # CI/CD para aplicación Python
├── node.yml              # CI/CD para aplicación Node.js
├── java.yml              # CI/CD para aplicación Java
└── php.yml               # CI/CD para aplicación PHP

applicacionRust/          # Aplicación del clima en Rust
aplicacionPython/         # Aplicación del clima en Python
aplicacionNode/           # Aplicación del clima en Node.js
aplicacionJava/           # Aplicación del clima en Java Spring Boot
aplicacionPhp/            # Aplicación del clima en PHP
```

## 🚀 Workflows Disponibles

### 1. Workflow General (monorepo.yml)
- **Propósito**: Coordina y detecta cambios en el monorepo
- **Ejecución**: En cada push/PR a main
- **Funcionalidades**:
  - Detecta qué aplicaciones han cambiado usando `paths-filter`
  - Muestra información general del repositorio
  - Coordina la ejecución de workflows específicos

### 2. Workflow Rust (rust.yml)
- **Trigger**: Cambios en `applicacionRust/**` o el propio workflow
- **Acciones**:
  - ✅ Setup de Rust con toolchain estable
  - ✅ Cache de dependencias Cargo
  - ✅ Verificación de formato (`cargo fmt`)
  - ✅ Linting con Clippy
  - ✅ Build en modo release
  - ✅ Ejecución de tests
  - 🐳 Build y push de imagen Docker

### 3. Workflow Python (python.yml)
- **Trigger**: Cambios en `aplicacionPython/**` o el propio workflow
- **Matrix Strategy**: Python 3.9, 3.10, 3.11
- **Acciones**:
  - ✅ Setup de Python con cache de pip
  - ✅ Instalación de dependencias
  - ✅ Linting con flake8 y pylint
  - ✅ Verificación de formato con Black
  - ✅ Tests con pytest
  - 🐳 Build y push de imagen Docker (solo main)

### 4. Workflow Node.js (node.yml)
- **Trigger**: Cambios en `aplicacionNode/**` o el propio workflow
- **Matrix Strategy**: Node.js 18.x, 20.x
- **Acciones**:
  - ✅ Setup de Node.js con cache de npm
  - ✅ Instalación de dependencias (`npm ci`)
  - ✅ Linting con ESLint
  - ✅ Verificación de formato con Prettier
  - ✅ Ejecución de tests
  - 🐳 Build y push de imagen Docker (solo main)

### 5. Workflow Java - Aplicación del Clima (java.yml)
- **Trigger**: Cambios en `aplicacionJava/**` o el propio workflow
- **Acciones**:
  - ✅ Setup de JDK 21 con cache de Maven
  - ✅ Build con Maven
  - ✅ Ejecución de tests con JUnit 5
  - ✅ Generación de reporte de cobertura con JaCoCo
  - ✅ Verificación de calidad de código
  - 🐳 Build y push de imagen Docker (solo main)

### 6. Workflow PHP - Aplicación del Clima (php.yml)
- **Trigger**: Cambios en `aplicacionPhp/**` o el propio workflow
- **Matrix Strategy**: PHP 8.1, 8.2, 8.3
- **Acciones**:
  - ✅ Setup de PHP con cache de Composer
  - ✅ Instalación de dependencias con Composer
  - ✅ Análisis estático con PHPStan
  - ✅ Verificación de estilo de código con PHP-CS-Fixer
  - ✅ Ejecución de tests con PHPUnit
  - ✅ Generación de reporte de cobertura
  - 🐳 Build y push de imagen Docker (solo main)

## 🔧 Configuración de Variables

Para que los workflows funcionen correctamente, configura estas variables en tu repositorio de GitHub:

### Secrets Requeridos
```bash
DOCKERHUB_USERNAME=tu-usuario-dockerhub
DOCKERHUB_TOKEN=tu-token-dockerhub
```

### Variables de Repositorio (Opcionales)
```bash
# Versionado para Rust
RUST_MAJOR_VERSION=1
RUST_MINOR_VERSION=0

# Versionado para Python
PYTHON_MAJOR_VERSION=1
PYTHON_MINOR_VERSION=0

# Versionado para Node.js
NODE_MAJOR_VERSION=1
NODE_MINOR_VERSION=0

# Versionado para Java (aplicación del clima)
JAVA_MAJOR_VERSION=1
JAVA_MINOR_VERSION=0

# Versionado para PHP
PHP_MAJOR_VERSION=1
PHP_MINOR_VERSION=0
```

## 📊 Versionado Automático

Cada aplicación genera versiones automáticamente usando el patrón:
```
{MAJOR}.{MINOR}.{GITHUB_RUN_NUMBER}
```

Por ejemplo: `1.0.42` donde 42 es el número de ejecución del workflow.

## 🎯 Optimizaciones del Monorepo

### 1. Ejecución Condicional
Los workflows solo se ejecutan cuando hay cambios en sus respectivas carpetas:
```yaml
on:
  push:
    paths:
      - 'aplicacionPython/**'
      - '.github/workflows/python.yml'
```

### 2. Cache Inteligente
Cada workflow usa cache específico para su lenguaje:
- **Rust**: Cache de Cargo registry y target
- **Python**: Cache de pip
- **Node.js**: Cache de npm
- **Java**: Cache de Maven

### 3. Matrix Strategy
Python y Node.js usan matrix para probar múltiples versiones:
```yaml
strategy:
  matrix:
    python-version: [3.9, 3.10, 3.11]
    node-version: [18.x, 20.x]
```

### 4. Jobs Separados
Los workflows de Python y Node.js separan testing y Docker build para optimizar recursos.

## 🐳 Imágenes Docker

Las imágenes se publican en Docker Hub con los siguientes nombres:
- `usuario/clima-app-rust:latest` y `usuario/clima-app-rust:version`
- `usuario/clima-app-python:latest` y `usuario/clima-app-python:version`
- `usuario/clima-app-node:latest` y `usuario/clima-app-node:version`
- `usuario/clima-app-java:latest` y `usuario/clima-app-java:version`
- `usuario/clima-app-php:latest` y `usuario/clima-app-php:version`

## 📈 Monitoreo y Logs

Cada workflow proporciona información detallada sobre:
- Versiones de herramientas utilizadas
- Resultados de tests y linting
- Métricas de build
- Estado de publicación de imágenes Docker

## 🔍 Debugging

Para debuggear problemas:

1. **Revisa los logs**: Cada step tiene logs detallados
2. **Verifica paths**: Asegúrate de que los cambios están en las carpetas correctas
3. **Confirma secrets**: Revisa que DOCKERHUB_USERNAME y DOCKERHUB_TOKEN estén configurados
4. **Valida sintaxis**: Usa un validador YAML para verificar la sintaxis de los workflows

## 🚀 Uso

1. **Push changes**: Haz push de cambios a cualquier aplicación
2. **Automatic execution**: Solo se ejecutarán los workflows de las aplicaciones modificadas
3. **Docker images**: Si el push es a `main`, se publicarán las imágenes Docker
4. **Monitor results**: Revisa la pestaña "Actions" en GitHub para ver el progreso

Este setup permite desarrollo independiente de cada aplicación mientras mantiene una integración continua eficiente para todo el monorepo.
