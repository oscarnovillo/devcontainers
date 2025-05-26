<?php

/**
 * Manejo de las peticiones API
 */

use App\Controller\ClimaController;
use App\Utils\Logger;

try {
    $controller = new ClimaController();
    $controller->handleRequest();
} catch (\Throwable $e) {
    Logger::error("Error crítico en API", [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => [
            'message' => 'Error interno del servidor',
            'code' => 'INTERNAL_SERVER_ERROR',
            'status' => 500
        ],
        'timestamp' => date('c')
    ]);
}
