# Imagen base para producción
FROM python:3.11-slim AS production

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Crear un usuario no-root para ejecutar la aplicación
RUN useradd -ms /bin/bash appuser

# Crear directorio para la aplicación
WORKDIR /app

# Copiar archivos de dependencias
COPY requirements.txt .

# Instalar dependencias de Python
RUN pip install --no-cache-dir -r requirements.txt

# Copiar el código de la aplicación
COPY app.py .

# Cambiar a usuario no-root por seguridad
USER appuser

ARG WEATHER_API_KEY
ENV WEATHER_API_KEY=${WEATHER_API_KEY}

# Ejecutar la aplicación
CMD ["python", "app.py"]
