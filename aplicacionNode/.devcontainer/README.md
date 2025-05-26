# DevContainer para Aplicación de Clima Node.js

Este directorio contiene la configuración del DevContainer para desarrollar la aplicación de clima en Node.js usando Visual Studio Code.

## ¿Qué es un DevContainer?

Un DevContainer (Development Container) es un contenedor Docker configurado específicamente para desarrollo que incluye todas las herramientas, extensiones y dependencias necesarias para trabajar en el proyecto.

## Características incluidas

### Herramientas de desarrollo
- Node.js 18 (Alpine Linux)
- npm y todas las dependencias del proyecto
- Git
- Curl
- OpenSSH Client

### Extensiones de VS Code
- JavaScript Debugger (Microsoft)
- TypeScript Support
- JSON Support
- YAML Support
- Docker
- Prettier (Formateador)
- ESLint (Linter)
- GitHub Copilot (si está disponible)
- npm Scripts

### Herramientas adicionales
- Nodemon (reinicio automático en desarrollo)
- ESLint (linting)
- Prettier (formateo de código)
- @types/node (tipados de Node.js)

## Cómo usar

1. **Prerrequisitos:**
   - Tener instalado VS Code
   - Tener instalada la extensión "Dev Containers" en VS Code
   - Tener Docker Desktop en funcionamiento

2. **Abrir en DevContainer:**
   - Abre la carpeta `aplicacionNode` en VS Code
   - VS Code detectará la configuración del DevContainer
   - Aparecerá una notificación para "Reopen in Container"
   - O usa el Command Palette (Ctrl+Shift+P) y busca "Dev Containers: Reopen in Container"

3. **Primera vez:**
   - El contenedor se construirá automáticamente
   - Se instalarán todas las dependencias con `npm install`
   - Se configurarán las extensiones

4. **Configurar variables de entorno:**
   - Crea un archivo `.env` en la raíz del proyecto con tu API key:
     ```
     WEATHER_API_KEY=tu_clave_api_aqui
     DEBUG=true
     LOG_LEVEL=debug
     NODE_ENV=development
     ```

## Configuración incluida

### Formateo automático
- Prettier configurado para formatear automáticamente al guardar
- Configuración en `.prettierrc`
- Comillas simples, semicolons, trailing commas

### Linting
- ESLint configurado con reglas estándar
- Configuración en `.eslintrc.js`
- Autofix al guardar habilitado

### Depuración
- Configuración de depuración lista para usar
- Variables de entorno de desarrollo preconfiguradas
- Puertos 3000 y 8080 forwarded automáticamente

## Estructura del DevContainer

```
.devcontainer/
├── devcontainer.json    # Configuración principal
└── Dockerfile          # Imagen personalizada para desarrollo
```

## Scripts disponibles

```bash
# Desarrollo con nodemon (reinicio automático)
npm run dev

# Ejecutar en modo producción
npm run prod

# Ejecutar normalmente
npm start

# Linting
npm run lint

# Linting con autofix
npm run lint:fix

# Formatear código
npm run format

# Verificar formato
npm run format:check
```

## Comandos útiles en el contenedor

```bash
# Instalar nueva dependencia
npm install package-name

# Instalar dependencia de desarrollo
npm install --save-dev package-name

# Verificar código
npm run lint
npm run format:check

# Arreglar formato y linting
npm run lint:fix
npm run format
```

## Configuración de VS Code

### Formateo automático
- Prettier como formateador por defecto
- Formateo automático al guardar
- Fix de ESLint automático al guardar

### Depuración
- Debugger de JavaScript/Node.js configurado
- Breakpoints y debugging completo disponible

## Puertos Forward

- **Puerto 3000:** Para aplicaciones web
- **Puerto 8080:** Para servidores de desarrollo

Estos puertos se abren automáticamente cuando el contenedor detecta servicios corriendo en ellos.

## Ventajas del DevContainer

- **Entorno consistente:** Todos los desarrolladores trabajan con el mismo entorno
- **Configuración automática:** No necesitas instalar Node.js, dependencias o extensiones manualmente
- **Aislamiento:** No afecta tu sistema local
- **Portabilidad:** Funciona igual en cualquier máquina con Docker
- **Productividad:** Todas las herramientas están preconfiguradas y listas para usar
- **Hot reload:** Nodemon para reinicio automático durante desarrollo
