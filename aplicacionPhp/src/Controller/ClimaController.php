<?php

namespace App\Controller;

use App\Service\ClimaService;
use App\Utils\Response;
use App\Utils\Logger;
use App\Config\AppConfig;

/**
 * Controlador para las operaciones relacionadas con el clima
 */
class ClimaController
{
    private ClimaService $climaService;
    private AppConfig $config;

    public function __construct()
    {
        $this->climaService = new ClimaService();
        $this->config = AppConfig::getInstance();
    }

    /**
     * Maneja las peticiones de la API
     */
    public function handleRequest(): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            
            // Limpiar query string de la URI
            $path = parse_url($uri, PHP_URL_PATH);
            
            Logger::info("Petición recibida", [
                'method' => $method,
                'path' => $path,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);

            // Manejar CORS preflight
            if ($method === 'OPTIONS') {
                Response::options();
                return;
            }

            // Enrutar según el path
            match (true) {
                $path === '/api/health' => $this->health(),
                str_starts_with($path, '/api/clima/') => $this->getClimaByCiudad($path, $method),
                $path === '/api/clima' => $this->getClimaFromBody($method),
                default => Response::notFound('Endpoint no encontrado')
            };

        } catch (\Throwable $e) {
            Logger::error("Error no manejado en el controlador", [
                'error' => $e->getMessage(),
                'trace' => $this->config->isDebugEnabled() ? $e->getTraceAsString() : null
            ]);

            Response::internalServerError(
                $this->config->isDebugEnabled() 
                    ? $e->getMessage() 
                    : 'Error interno del servidor'
            );
        }
    }

    /**
     * Endpoint de salud
     */
    private function health(): void
    {
        try {
            $healthData = [
                'service' => 'clima-app-php',
                'environment' => $this->config->getEnvironment()
            ];

            // Verificar configuración del servicio
            $configIssues = $this->climaService->validateConfiguration();
            if (!empty($configIssues)) {
                $healthData['config_issues'] = $configIssues;
                Response::json([
                    'status' => 'degraded',
                    'timestamp' => date('c'),
                    'version' => $this->config->getAppVersion(),
                    'data' => $healthData
                ], 200);
                return;
            }

            // Verificar conectividad con la API externa
            $apiHealth = $this->climaService->healthCheck();
            $healthData['external_api'] = $apiHealth;

            $status = $apiHealth['status'] === 'ok' ? 'ok' : 'degraded';

            Response::health($status, $healthData);

        } catch (\Throwable $e) {
            Logger::error("Error en health check", ['error' => $e->getMessage()]);
            Response::json([
                'status' => 'error',
                'timestamp' => date('c'),
                'version' => $this->config->getAppVersion(),
                'error' => 'Health check failed'
            ], 503);
        }
    }

    /**
     * Obtiene clima desde parámetro de ruta
     */
    private function getClimaByCiudad(string $path, string $method): void
    {
        if ($method !== 'GET') {
            Response::error('Método no permitido', 405, 'METHOD_NOT_ALLOWED');
            return;
        }

        // Extraer ciudad del path: /api/clima/{ciudad}
        $pathParts = explode('/', trim($path, '/'));
        if (count($pathParts) < 3) {
            Response::badRequest('Ciudad no especificada en la URL');
            return;
        }

        $ciudad = urldecode($pathParts[2]);
        $this->obtenerClima($ciudad);
    }

    /**
     * Obtiene clima desde el cuerpo de la petición
     */
    private function getClimaFromBody(string $method): void
    {
        if ($method !== 'POST') {
            Response::error('Método no permitido', 405, 'METHOD_NOT_ALLOWED');
            return;
        }

        try {
            $input = file_get_contents('php://input');
            if (empty($input)) {
                Response::badRequest('Cuerpo de la petición vacío');
                return;
            }

            $data = json_decode($input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Response::badRequest('JSON inválido: ' . json_last_error_msg());
                return;
            }

            if (!isset($data['ciudad']) || empty(trim($data['ciudad']))) {
                Response::badRequest('Campo "ciudad" requerido');
                return;
            }

            $ciudad = trim($data['ciudad']);
            $this->obtenerClima($ciudad);

        } catch (\Throwable $e) {
            Logger::error("Error procesando petición POST", [
                'error' => $e->getMessage()
            ]);
            Response::internalServerError('Error procesando la petición');
        }
    }

    /**
     * Lógica común para obtener clima
     */
    private function obtenerClima(string $ciudad): void
    {
        try {
            // Validar entrada
            if (empty($ciudad)) {
                Response::badRequest('La ciudad no puede estar vacía');
                return;
            }

            if (strlen($ciudad) > 100) {
                Response::badRequest('El nombre de la ciudad es demasiado largo');
                return;
            }

            // Sanitizar entrada
            $ciudad = $this->sanitizeCiudad($ciudad);

            Logger::info("Procesando solicitud de clima", ['ciudad' => $ciudad]);

            // Obtener información del clima
            $climaInfo = $this->climaService->obtenerClima($ciudad);

            if (!$climaInfo->isValid()) {
                Response::internalServerError('Datos de clima inválidos recibidos');
                return;
            }

            Response::success(
                $climaInfo->toArray(),
                'Información del clima obtenida exitosamente'
            );

        } catch (\InvalidArgumentException $e) {
            Logger::info("Error de validación", [
                'ciudad' => $ciudad,
                'error' => $e->getMessage()
            ]);
            Response::badRequest($e->getMessage());

        } catch (\RuntimeException $e) {
            Logger::warning("Error del servicio", [
                'ciudad' => $ciudad,
                'error' => $e->getMessage()
            ]);

            // Determinar código de error apropiado
            $statusCode = str_contains($e->getMessage(), 'no encontrada') ? 404 : 503;
            Response::error($e->getMessage(), $statusCode, 'SERVICE_ERROR');

        } catch (\Throwable $e) {
            Logger::error("Error inesperado obteniendo clima", [
                'ciudad' => $ciudad,
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);

            Response::internalServerError(
                $this->config->isDebugEnabled() 
                    ? $e->getMessage() 
                    : 'Error interno del servidor'
            );
        }
    }

    /**
     * Sanitiza el nombre de la ciudad
     */
    private function sanitizeCiudad(string $ciudad): string
    {
        // Eliminar caracteres no deseados pero mantener caracteres internacionales
        $ciudad = trim($ciudad);
        $ciudad = preg_replace('/[<>"\']/', '', $ciudad);
        
        return $ciudad;
    }

    /**
     * Valida el formato de una ciudad
     */
    private function isValidCiudad(string $ciudad): bool
    {
        // Verificar longitud
        if (strlen($ciudad) < 1 || strlen($ciudad) > 100) {
            return false;
        }

        // Verificar que no contenga solo espacios o caracteres especiales
        if (preg_match('/^[\s\-_.,]+$/', $ciudad)) {
            return false;
        }

        return true;
    }

    /**
     * Obtiene información de depuración del servicio
     */
    public function debug(): void
    {
        if (!$this->config->isDevelopment()) {
            Response::notFound();
            return;
        }

        $debugInfo = [
            'config' => [
                'environment' => $this->config->getEnvironment(),
                'debug' => $this->config->isDebugEnabled(),
                'api_configured' => !empty($this->config->getWeatherApiKey()),
                'base_url' => $this->config->getApiBaseUrl()
            ],
            'service' => [
                'config_issues' => $this->climaService->validateConfiguration(),
                'health' => $this->climaService->healthCheck()
            ],
            'request' => [
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]
        ];

        Response::success($debugInfo, 'Debug information');
    }
}
