# GitHub Actions Workflow para Aplicación Rust

Este directorio contiene el workflow de GitHub Actions para automatizar el CI/CD de la aplicación de clima en Rust.

## 🚀 Características del Workflow

### Triggers (Disparadores)
- **Push** a la rama `main`
- **Pull Requests** hacia la rama `main`
- **Tags** con formato `v*` (ej: v1.0.0, v1.2.3)

### Jobs y Pasos

#### 1. **Setup del entorno**
- Checkout del código
- Configuración de Rust (toolchain stable)
- Cache de dependencias de Cargo

#### 2. **Build y Testing**
- Build en modo release con `cargo build --release`
- Ejecución de tests con `cargo test`
- Linting con Clippy (`cargo clippy`)
- Verificación de formato con `cargo fmt --check`

#### 3. **Docker Build & Push**
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

### Generar Token de Docker Hub

1. Ve a [Docker Hub](https://hub.docker.com/)
2. **Account Settings > Security > New Access Token**
3. Crear token con permisos de **Read, Write, Delete**
4. Copiar el token y añadirlo como secreto en GitHub

## 📦 Imágenes Docker Generadas

El workflow genera estas imágenes:

```
tu-usuario/clima-app-rust:latest
tu-usuario/clima-app-rust:1.0.{run_number}
```

Donde `{run_number}` es el número de ejecución del workflow.

## 🔧 Personalización

### Cambiar nombre de la imagen
Edita el archivo `.github/workflows/rust.yml`:

```yaml
tags: |
  ${{ secrets.DOCKERHUB_USERNAME }}/tu-nombre-imagen:latest
  ${{ secrets.DOCKERHUB_USERNAME }}/tu-nombre-imagen:${{ steps.version.outputs.VERSION }}
```

### Añadir más herramientas de linting
```yaml
- name: Run additional tools
  run: |
    cd applicacionRust/clima_app
    cargo audit  # Auditoría de seguridad
    cargo outdated  # Dependencias desactualizadas
```

### Cambiar versión de Rust
```yaml
- name: Setup Rust
  uses: actions-rs/toolchain@v1
  with:
    toolchain: 1.70.0  # Versión específica
    override: true
```

## 🐛 Solución de Problemas

### Error: "No se puede encontrar Cargo.toml"
- Verifica que la ruta `applicacionRust/clima_app` sea correcta
- Asegúrate de que el archivo `Cargo.toml` existe

### Error: "Tests fallan"
- Revisa los tests localmente: `cargo test`
- Verifica las dependencias en `Cargo.toml`

### Error: "Docker build falla"
- Verifica el `Dockerfile` en `applicacionRust/clima_app/`
- Revisa los logs del workflow para errores específicos

### Error: "No se puede hacer push a Docker Hub"
- Verifica que los secretos `DOCKERHUB_USERNAME` y `DOCKERHUB_TOKEN` estén configurados
- Asegúrate de que el token tenga permisos de escritura

## 📊 Ejemplo de Ejecución

```
✅ Checkout code
✅ Setup Rust toolchain
✅ Cache cargo dependencies  
✅ Build with cargo (release mode)
✅ Run tests
✅ Run clippy linting
✅ Check code formatting
✅ Setup Docker Buildx
✅ Login to Docker Hub
✅ Build and push Docker image
   📦 imagen-usuario/clima-app-rust:latest
   📦 imagen-usuario/clima-app-rust:1.0.42
```

## 🚀 Activación del Workflow

El workflow se ejecuta automáticamente cuando:

1. **Haces push** a la rama main
2. **Abres un Pull Request** hacia main
3. **Creas un tag** con formato `v*`

Para activar manualmente:
- Ve a **Actions** en GitHub
- Selecciona el workflow
- Click en **Run workflow**
