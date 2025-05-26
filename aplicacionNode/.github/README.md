# GitHub Actions Workflow para Aplicación Node.js

Este directorio contiene el workflow de GitHub Actions para automatizar el CI/CD de la aplicación de clima en Node.js.

## 🚀 Características del Workflow

### Triggers (Disparadores)
- **Push** a la rama `main`
- **Pull Requests** hacia la rama `main`
- **Tags** con formato `v*` (ej: v1.0.0, v1.2.3)

### Matrix Strategy
El workflow ejecuta en múltiples versiones de Node.js:
- **Node.js 18.x** (LTS)
- **Node.js 20.x** (Current)

### Jobs y Pasos

#### 1. **Setup del entorno**
- Checkout del código
- Configuración de Node.js (matrix: 18.x, 20.x)
- Cache de dependencias npm

#### 2. **Instalación y verificación**
- Instalación con `npm ci` (instalación limpia)
- Linting con **ESLint**
- Verificación de formato con **Prettier**

#### 3. **Testing**
- Ejecución de tests básicos
- Test de carga del módulo principal

#### 4. **Docker Build & Push** (solo Node.js 18.x)
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

El workflow genera estas imágenes (solo en Node.js 18.x):

```
tu-usuario/clima-app-node:latest
tu-usuario/clima-app-node:1.0.{run_number}
```

## 🛠️ Herramientas de Calidad

### **ESLint** - Linting
- Detecta errores de JavaScript
- Verifica mejores prácticas
- Configuración en `.eslintrc.js`

### **Prettier** - Formateo
- Formateo consistente del código
- Configuración en `.prettierrc`
- Integración con ESLint

### Scripts disponibles:
```json
{
  "scripts": {
    "lint": "eslint .",
    "lint:fix": "eslint . --fix",
    "format": "prettier --write .",
    "format:check": "prettier --check ."
  }
}
```

## 🔧 Personalización

### Añadir más versiones de Node.js
```yaml
strategy:
  matrix:
    node-version: [16.x, 18.x, 20.x, 21.x]
```

### Configurar tests reales
Crea archivos de test:

```javascript
// test/app.test.js
const assert = require('assert');
const { obtenerClima } = require('../app');

describe('App tests', function() {
  it('should load app module', function() {
    assert.ok(require('../app'));
  });
});
```

Actualiza package.json:
```json
{
  "scripts": {
    "test": "mocha test/*.js"
  },
  "devDependencies": {
    "mocha": "^10.0.0"
  }
}
```

### Añadir análisis de seguridad
```yaml
- name: Run security audit
  run: |
    cd aplicacionNode
    npm audit --audit-level=high
```

### Configurar coverage
```yaml
- name: Run tests with coverage
  run: |
    cd aplicacionNode
    npm run test:coverage
```

## 🐛 Solución de Problemas

### Error: "ESLint encuentra errores"
```bash
# Ver errores localmente
cd aplicacionNode
npm run lint

# Autofix errores
npm run lint:fix
```

### Error: "Prettier encuentra diferencias"
```bash
# Formatear código
cd aplicacionNode
npm run format

# Verificar formato
npm run format:check
```

### Error: "npm ci falla"
```bash
# Limpiar cache y reinstalar
rm -rf node_modules package-lock.json
npm install
```

### Error: "Docker build falla"
- Verifica que el `Dockerfile` esté en la carpeta correcta
- Revisa que `package.json` tenga todas las dependencias

## 📊 Ejemplo de Ejecución

```
Matrix Job: Node.js 18.x
✅ Checkout code
✅ Setup Node.js 18.x
✅ Install dependencies (npm ci)
✅ Run linting (ESLint)
✅ Check code formatting (Prettier)
✅ Run tests
✅ Setup Docker Buildx
✅ Login to Docker Hub
✅ Build and push Docker image
   📦 usuario/clima-app-node:latest
   📦 usuario/clima-app-node:1.0.42

Matrix Job: Node.js 20.x
✅ Checkout code
✅ Setup Node.js 20.x
✅ Install dependencies (npm ci)
✅ Run linting (ESLint)
✅ Check code formatting (Prettier)
✅ Run tests
❌ Docker steps skipped (only on 18.x)
```

## 🔍 Ventajas del Matrix Build

- **Compatibilidad**: Verifica que funciona en múltiples versiones
- **Early detection**: Detecta problemas de compatibilidad temprano
- **Flexibility**: Permite usar features específicas de versiones

## 📝 Configuración recomendada para package.json

```json
{
  "engines": {
    "node": ">=18.0.0",
    "npm": ">=8.0.0"
  },
  "scripts": {
    "start": "node app.js",
    "dev": "nodemon app.js",
    "test": "mocha test/*.js",
    "lint": "eslint .",
    "lint:fix": "eslint . --fix",
    "format": "prettier --write .",
    "format:check": "prettier --check ."
  }
}
```

## 🚀 Activación del Workflow

El workflow se ejecuta automáticamente cuando:

1. **Haces push** a la rama main
2. **Abres un Pull Request** hacia main
3. **Creas un tag** con formato `v*`

Para activar manualmente:
- Ve a **Actions** en GitHub
- Selecciona el workflow "Node.js CI with npm and Docker"
- Click en **Run workflow**
