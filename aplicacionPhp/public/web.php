<?php

/**
 * Interfaz web de la aplicación
 */

use App\Config\AppConfig;

$config = AppConfig::getInstance();
$title = $config->getAppName();
$version = $config->getAppVersion();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header-content">
                <h1><i class="fas fa-cloud-sun"></i> <?php echo htmlspecialchars($title); ?></h1>
                <p class="subtitle">Información meteorológica en tiempo real</p>
                <div class="version">v<?php echo htmlspecialchars($version); ?></div>
            </div>
        </header>

        <main class="main">
            <div class="search-section">
                <h2>Consultar Clima</h2>
                <form id="climaForm" class="search-form">
                    <div class="input-group">
                        <i class="fas fa-map-marker-alt"></i>
                        <input 
                            type="text" 
                            id="ciudadInput" 
                            placeholder="Ingresa el nombre de una ciudad..." 
                            required
                            autocomplete="off"
                        >
                        <button type="submit" id="buscarBtn">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                    </div>
                </form>
            </div>

            <div id="resultado" class="result-section" style="display: none;"></div>
            <div id="loading" class="loading" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Obteniendo información meteorológica...</span>
            </div>
            <div id="error" class="error-section" style="display: none;"></div>
        </main>

        <section class="examples-section">
            <h3>Ejemplos de ciudades</h3>
            <div class="examples-grid">
                <button class="example-btn" onclick="buscarCiudad('Madrid')">
                    <i class="fas fa-city"></i> Madrid
                </button>
                <button class="example-btn" onclick="buscarCiudad('Barcelona')">
                    <i class="fas fa-city"></i> Barcelona
                </button>
                <button class="example-btn" onclick="buscarCiudad('Valencia')">
                    <i class="fas fa-city"></i> Valencia
                </button>
                <button class="example-btn" onclick="buscarCiudad('Sevilla')">
                    <i class="fas fa-city"></i> Sevilla
                </button>
                <button class="example-btn" onclick="buscarCiudad('London')">
                    <i class="fas fa-globe"></i> Londres
                </button>
                <button class="example-btn" onclick="buscarCiudad('Paris')">
                    <i class="fas fa-globe"></i> París
                </button>
                <button class="example-btn" onclick="buscarCiudad('New York')">
                    <i class="fas fa-globe"></i> Nueva York
                </button>
                <button class="example-btn" onclick="buscarCiudad('Tokyo')">
                    <i class="fas fa-globe"></i> Tokio
                </button>
            </div>
        </section>

        <section class="api-section">
            <h3>API Endpoints</h3>
            <div class="api-grid">
                <div class="api-card">
                    <h4><i class="fas fa-heartbeat"></i> Health Check</h4>
                    <code>GET /api/health</code>
                    <p>Verifica el estado de la aplicación</p>
                    <button onclick="testEndpoint('/api/health')" class="test-btn">
                        <i class="fas fa-play"></i> Probar
                    </button>
                </div>
                
                <div class="api-card">
                    <h4><i class="fas fa-cloud"></i> Clima por URL</h4>
                    <code>GET /api/clima/{ciudad}</code>
                    <p>Obtiene clima usando parámetro en la URL</p>
                    <button onclick="testEndpoint('/api/clima/Madrid')" class="test-btn">
                        <i class="fas fa-play"></i> Probar
                    </button>
                </div>
                
                <div class="api-card">
                    <h4><i class="fas fa-cloud-rain"></i> Clima por POST</h4>
                    <code>POST /api/clima</code>
                    <p>Obtiene clima enviando ciudad en el cuerpo</p>
                    <button onclick="testEndpointPost('/api/clima', 'Barcelona')" class="test-btn">
                        <i class="fas fa-play"></i> Probar
                    </button>
                </div>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2025 Aplicación de Clima PHP. Datos proporcionados por <a href="https://www.weatherapi.com/" target="_blank">WeatherAPI</a></p>
            <div class="footer-links">
                <a href="https://github.com" target="_blank"><i class="fab fa-github"></i> GitHub</a>
                <a href="/api/health" target="_blank"><i class="fas fa-heartbeat"></i> Status</a>
            </div>
        </div>
    </footer>

    <script src="/script.js"></script>
</body>
</html>
