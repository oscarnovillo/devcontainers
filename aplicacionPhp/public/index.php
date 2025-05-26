<?php

/**
 * Punto de entrada principal de la aplicación PHP
 */

// Configurar el manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en producción

// Configurar timezone por defecto
date_default_timezone_set('UTC');

// Cargar autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar configuración
try {
    $config = \App\Config\AppConfig::getInstance();
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Error de configuración: ' . $e->getMessage(),
        'timestamp' => date('c')
    ]);
    exit;
}

// Configurar display_errors según el entorno
ini_set('display_errors', $config->isDevelopment() ? 1 : 0);

// Determinar si es una petición de API o web
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

if (str_starts_with($path, '/api/')) {
    // Manejar peticiones de API
    require_once __DIR__ . '/api.php';
} else {
    // Servir interfaz web
    require_once __DIR__ . '/web.php';
}
