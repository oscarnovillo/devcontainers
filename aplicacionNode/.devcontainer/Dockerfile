# Imagen de desarrollo para devcontainer
FROM node:18-bullseye

# Instalar dependencias del sistema necesarias
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    bash \
    ca-certificates \
    curl \
    git \
    openssh-client \
    && rm -rf /var/lib/apt/lists/*

# Crear directorio para la aplicación
WORKDIR /app

# Copiar archivos de dependencias
COPY package*.json ./

# Instalar dependencias de Node.js
RUN npm install

# Instalar herramientas de desarrollo globales
RUN npm install -g \
    nodemon \
    eslint \
    prettier \
    @types/node

# Configurar Git para el contenedor (opcional)
RUN git config --global --add safe.directory /app

# Crear usuario node si no existe y configurar permisos
RUN if ! id -u node > /dev/null 2>&1; then \
        addgroup --gid 1000 node && \
        adduser --uid 1000 --gid 1000 --shell /bin/bash --disabled-password --gecos "" node; \
    fi && \
    chown -R node:node /app

# Cambiar al usuario node
USER node

# Mantener el contenedor en ejecución
CMD ["sleep", "infinity"]
