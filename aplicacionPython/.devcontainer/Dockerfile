# Imagen de desarrollo para devcontainer
FROM python:3.11-slim

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    ca-certificates \
    curl \
    git \
    build-essential \
    pkg-config \
    docker.io \
    && rm -rf /var/lib/apt/lists/*

# Crear directorio para la aplicación
WORKDIR /app

# Copiar archivos de dependencias
COPY requirements.txt .

# Instalar dependencias de Python
RUN pip install --no-cache-dir -r requirements.txt

# Instalar herramientas de desarrollo adicionales
RUN pip install --no-cache-dir \
    black \
    flake8 \
    pylint \
    isort \
    pytest \
    ipython \
    jupyter

# Instalar extensiones útiles para desarrollo
RUN pip install --no-cache-dir \
    python-dotenv \
    requests

# Configurar Git para el contenedor (opcional)
RUN git config --global --add safe.directory /app

# Mantener el contenedor en ejecución
CMD ["sleep", "infinity"]
