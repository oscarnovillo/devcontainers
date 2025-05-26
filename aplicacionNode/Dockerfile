# Imagen base para producción
FROM node:18-alpine AS production

# Instalar dependencias del sistema necesarias
RUN apk add --no-cache \
    ca-certificates \
    && rm -rf /var/cache/apk/*

# Crear un usuario no-root para ejecutar la aplicación
RUN addgroup -g 1001 -S nodejs && \
    adduser -S appuser -u 1001 -G nodejs

# Crear directorio para la aplicación
WORKDIR /app

# Copiar archivos de dependencias
COPY package*.json ./

# Instalar solo dependencias de producción
RUN npm ci --only=production && npm cache clean --force

# Copiar el código de la aplicación
COPY app.js .

# Cambiar propietario de los archivos al usuario no-root
RUN chown -R appuser:nodejs /app

# Cambiar a usuario no-root por seguridad
USER appuser

ARG WEATHER_API_KEY
ENV WEATHER_API_KEY=${WEATHER_API_KEY}

# Ejecutar la aplicación
CMD ["node", "app.js"]
