# Dockerfile para DevContainer Java
FROM mcr.microsoft.com/devcontainers/java:1-21-bullseye

# Instalar Maven (si no está incluido)
RUN apt-get update && apt-get install -y maven && rm -rf /var/lib/apt/lists/*

# Crear carpeta de trabajo
WORKDIR /workspace

# Copiar archivos de dependencias para aprovechar la cache
COPY ../pom.xml ./
RUN mvn dependency:go-offline

# Copiar el resto del código
COPY .. .

# Permisos para el usuario vscode
RUN chown -R vscode:vscode /workspace

USER vscode
