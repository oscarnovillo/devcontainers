/**
 * JavaScript para la aplicación de clima PHP
 */

class ClimaApp {
    constructor() {
        this.form = document.getElementById('climaForm');
        this.ciudadInput = document.getElementById('ciudadInput');
        this.buscarBtn = document.getElementById('buscarBtn');
        this.resultadoDiv = document.getElementById('resultado');
        this.loadingDiv = document.getElementById('loading');
        this.errorDiv = document.getElementById('error');
        
        this.init();
    }

    init() {
        // Event listeners
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        this.ciudadInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.handleSubmit(e);
            }
        });

        // Limpiar mensajes al escribir
        this.ciudadInput.addEventListener('input', () => {
            this.hideError();
        });

        // Focus inicial
        this.ciudadInput.focus();
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        const ciudad = this.ciudadInput.value.trim();
        if (!ciudad) {
            this.showError('Por favor, ingresa el nombre de una ciudad');
            return;
        }

        await this.buscarClima(ciudad);
    }

    async buscarClima(ciudad) {
        try {
            this.showLoading();
            this.hideError();
            this.hideResult();

            // Realizar petición
            const response = await fetch(`/api/clima/${encodeURIComponent(ciudad)}`);
            const data = await response.json();

            this.hideLoading();

            if (data.success) {
                this.showResult(data.data);
            } else {
                this.showError(data.error.message || 'Error al obtener información del clima');
            }

        } catch (error) {
            this.hideLoading();
            console.error('Error:', error);
            this.showError('Error de conexión. Por favor, inténtalo de nuevo.');
        }
    }

    showResult(data) {
        const html = this.buildWeatherHTML(data);
        this.resultadoDiv.innerHTML = html;
        this.resultadoDiv.style.display = 'block';
        
        // Scroll suave al resultado
        this.resultadoDiv.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }

    buildWeatherHTML(data) {
        const iconUrl = data.icono ? `https:${data.icono}` : null;
        
        return `
            <div class="weather-card">
                <div class="weather-icon">
                    ${iconUrl ? 
                        `<img src="${iconUrl}" alt="${data.descripcion}" />` : 
                        '<i class="fas fa-cloud"></i>'
                    }
                </div>
                
                <div class="weather-info">
                    <h3>${this.escapeHtml(data.descripcion)}</h3>
                    <div class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        ${this.escapeHtml(data.ciudad)}, ${this.escapeHtml(data.region)}, ${this.escapeHtml(data.pais)}
                    </div>
                </div>
                
                <div class="weather-temp">
                    <div class="temp">${data.temperatura}°C</div>
                    <div class="feels-like">Sensación: ${data.sensacion_termica}°C</div>
                </div>
            </div>
            
            <div class="weather-details">
                <div class="detail-item">
                    <i class="fas fa-tint"></i>
                    <span>Humedad: ${data.humedad}%</span>
                </div>
                
                ${data.viento_kph ? `
                    <div class="detail-item">
                        <i class="fas fa-wind"></i>
                        <span>Viento: ${data.viento_kph} km/h ${data.direccion_viento || ''}</span>
                    </div>
                ` : ''}
                
                ${data.presion_mb ? `
                    <div class="detail-item">
                        <i class="fas fa-thermometer-half"></i>
                        <span>Presión: ${data.presion_mb} mb</span>
                    </div>
                ` : ''}
                
                ${data.visibilidad_km ? `
                    <div class="detail-item">
                        <i class="fas fa-eye"></i>
                        <span>Visibilidad: ${data.visibilidad_km} km</span>
                    </div>
                ` : ''}
                
                ${data.indice_uv ? `
                    <div class="detail-item">
                        <i class="fas fa-sun"></i>
                        <span>Índice UV: ${data.indice_uv}</span>
                    </div>
                ` : ''}
                
                <div class="detail-item">
                    <i class="fas fa-clock"></i>
                    <span>Actualizado: ${this.formatTimestamp(data.timestamp)}</span>
                </div>
            </div>
        `;
    }

    showError(message) {
        this.errorDiv.innerHTML = `
            <h3><i class="fas fa-exclamation-triangle"></i> Error</h3>
            <p>${this.escapeHtml(message)}</p>
        `;
        this.errorDiv.style.display = 'block';
    }

    showLoading() {
        this.loadingDiv.style.display = 'block';
        this.buscarBtn.disabled = true;
        this.buscarBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando...';
    }

    hideLoading() {
        this.loadingDiv.style.display = 'none';
        this.buscarBtn.disabled = false;
        this.buscarBtn.innerHTML = '<i class="fas fa-search"></i> Buscar';
    }

    hideResult() {
        this.resultadoDiv.style.display = 'none';
    }

    hideError() {
        this.errorDiv.style.display = 'none';
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    formatTimestamp(timestamp) {
        try {
            const date = new Date(timestamp);
            return date.toLocaleString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return timestamp;
        }
    }
}

// Funciones globales para los botones de ejemplo
window.buscarCiudad = function(ciudad) {
    const app = window.climaApp;
    if (app) {
        app.ciudadInput.value = ciudad;
        app.buscarClima(ciudad);
    }
};

// Función para probar endpoints
window.testEndpoint = async function(endpoint) {
    try {
        const response = await fetch(endpoint);
        const data = await response.json();
        
        // Mostrar resultado en una ventana modal simple
        const result = JSON.stringify(data, null, 2);
        const newWindow = window.open('', '_blank', 'width=600,height=400,scrollbars=yes');
        newWindow.document.write(`
            <html>
                <head>
                    <title>API Response - ${endpoint}</title>
                    <style>
                        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
                        pre { background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd; overflow: auto; }
                        h3 { color: #333; }
                    </style>
                </head>
                <body>
                    <h3>Respuesta de: ${endpoint}</h3>
                    <pre>${result}</pre>
                </body>
            </html>
        `);
    } catch (error) {
        alert('Error al probar el endpoint: ' + error.message);
    }
};

// Función para probar endpoint POST
window.testEndpointPost = async function(endpoint, ciudad) {
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ciudad: ciudad })
        });
        
        const data = await response.json();
        
        // Mostrar resultado en una ventana modal simple
        const result = JSON.stringify(data, null, 2);
        const newWindow = window.open('', '_blank', 'width=600,height=400,scrollbars=yes');
        newWindow.document.write(`
            <html>
                <head>
                    <title>API Response - ${endpoint}</title>
                    <style>
                        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
                        pre { background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd; overflow: auto; }
                        h3 { color: #333; }
                    </style>
                </head>
                <body>
                    <h3>Respuesta de: POST ${endpoint}</h3>
                    <p><strong>Cuerpo enviado:</strong> {"ciudad": "${ciudad}"}</p>
                    <pre>${result}</pre>
                </body>
            </html>
        `);
    } catch (error) {
        alert('Error al probar el endpoint: ' + error.message);
    }
};

// Inicializar la aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.climaApp = new ClimaApp();
    
    // Agregar algunos listeners adicionales para mejorar la UX
    
    // Manejar errores de imágenes
    document.addEventListener('error', function(e) {
        if (e.target.tagName === 'IMG' && e.target.src.includes('weatherapi')) {
            e.target.style.display = 'none';
            const icon = document.createElement('i');
            icon.className = 'fas fa-cloud';
            e.target.parentNode.appendChild(icon);
        }
    }, true);
    
    // Agregar shortcuts de teclado
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K para enfocar el input de búsqueda
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            window.climaApp.ciudadInput.focus();
            window.climaApp.ciudadInput.select();
        }
    });
    
    console.log('Aplicación de Clima PHP inicializada correctamente');
});
