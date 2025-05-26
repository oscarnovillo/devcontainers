# Imagen base para compilar
FROM rust:1.86-slim AS builder

# Instalar dependencias necesarias para compilar
RUN apt-get update && apt-get install -y \
    pkg-config \
    libssl-dev \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

# Crear directorio para la aplicación
WORKDIR /usr/src/app

# Copiar los archivos del proyecto
COPY Cargo.toml Cargo.lock ./
# Crear directorios src vacíos para aprovechar la caché de dependencias
RUN mkdir -p src && \
    echo "fn main() {}" > src/main.rs && \
    cargo build --release && \
    rm -rf src

# Ahora copiar los archivos reales del proyecto
COPY src ./src

# Compilar la aplicación en modo release
RUN touch src/main.rs && cargo build --release

# Imagen final más ligera
FROM debian:bookworm-slim

# Instalar dependencias necesarias para SSL
RUN apt-get update && apt-get install -y \
    ca-certificates \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Crear un usuario no-root para ejecutar la aplicación
RUN useradd -ms /bin/bash appuser

# Crear directorio para la aplicación y el archivo .env
WORKDIR /app

# Copiar solo el binario compilado desde el builder
COPY --from=builder /usr/src/app/target/release/clima_app /app/

# En producción, no copiamos el .env ya que usaremos variables de entorno
# inyectadas por GitHub Actions

# Cambiar a usuario no-root por seguridad
USER appuser

ARG WEATHER_API_KEY
ENV WEATHER_API_KEY=${WEATHER_API_KEY}

# Ejecutar la aplicación
CMD ["/app/clima_app"]