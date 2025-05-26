<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use App\Config\AppConfig;
use App\Model\WeatherResponse;
use App\Model\ClimaInfo;
use App\Utils\Logger;

/**
 * Servicio para obtener información meteorológica
 */
class ClimaService
{
    private Client $httpClient;
    private AppConfig $config;
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->config = AppConfig::getInstance();
        $this->apiKey = $this->config->getWeatherApiKey();
        $this->baseUrl = $this->config->getApiBaseUrl();
        
        $this->httpClient = new Client([
            'timeout' => $this->config->getApiTimeout(),
            'connect_timeout' => $this->config->getApiConnectTimeout(),
            'headers' => [
                'User-Agent' => 'ClimaApp-PHP/' . $this->config->getAppVersion(),
                'Accept' => 'application/json'
            ]
        ]);

        Logger::info('ClimaService inicializado', [
            'base_url' => $this->baseUrl,
            'timeout' => $this->config->getApiTimeout()
        ]);
    }

    /**
     * Obtiene información del clima para una ciudad
     */
    public function obtenerClima(string $ciudad): ClimaInfo
    {
        $ciudad = trim($ciudad);
        
        if (empty($ciudad)) {
            throw new \InvalidArgumentException('La ciudad no puede estar vacía');
        }

        Logger::info("Solicitando clima para ciudad", ['ciudad' => $ciudad]);

        try {
            $weatherData = $this->makeApiRequest($ciudad);
            $weatherResponse = WeatherResponse::fromArray($weatherData);
            $climaInfo = ClimaInfo::fromWeatherResponse($weatherResponse);

            Logger::info("Clima obtenido exitosamente", [
                'ciudad' => $climaInfo->ciudad,
                'temperatura' => $climaInfo->temperatura,
                'descripcion' => $climaInfo->descripcion
            ]);

            return $climaInfo;

        } catch (\InvalidArgumentException $e) {
            Logger::warning("Error de validación al obtener clima", [
                'ciudad' => $ciudad,
                'error' => $e->getMessage()
            ]);
            throw $e;
        } catch (RequestException $e) {
            $this->handleRequestException($e, $ciudad);
        } catch (GuzzleException $e) {
            Logger::error("Error HTTP al obtener clima", [
                'ciudad' => $ciudad,
                'error' => $e->getMessage(),
                'trace' => $this->config->isDebugEnabled() ? $e->getTraceAsString() : null
            ]);
            throw new \RuntimeException(
                'Error de conexión con el servicio meteorológico. Inténtalo más tarde.',
                0,
                $e
            );
        } catch (\Throwable $e) {
            Logger::error("Error inesperado al obtener clima", [
                'ciudad' => $ciudad,
                'error' => $e->getMessage(),
                'type' => get_class($e),
                'trace' => $this->config->isDebugEnabled() ? $e->getTraceAsString() : null
            ]);
            throw new \RuntimeException(
                'Error interno del servidor. Inténtalo más tarde.',
                0,
                $e
            );
        }
    }

    /**
     * Realiza la petición a la API
     */
    private function makeApiRequest(string $ciudad): array
    {
        $url = $this->buildApiUrl($ciudad);
        
        Logger::debug("Realizando petición a WeatherAPI", [
            'url' => $url,
            'ciudad' => $ciudad
        ]);

        $response = $this->httpClient->get($url);
        
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(
                "Error en la API: código de estado {$response->getStatusCode()}"
            );
        }

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                'Error al decodificar la respuesta JSON: ' . json_last_error_msg()
            );
        }

        if (!$this->isValidApiResponse($data)) {
            throw new \RuntimeException(
                'Respuesta de la API en formato inválido'
            );
        }

        return $data;
    }

    /**
     * Construye la URL de la API
     */
    private function buildApiUrl(string $ciudad): string
    {
        $params = http_build_query([
            'key' => $this->apiKey,
            'q' => $ciudad,
            'aqi' => 'no',
            'lang' => 'es'
        ]);

        return "{$this->baseUrl}/current.json?{$params}";
    }

    /**
     * Valida que la respuesta de la API tenga el formato esperado
     */
    private function isValidApiResponse(array $data): bool
    {
        return isset($data['location']) && 
               isset($data['current']) &&
               isset($data['location']['name']) &&
               isset($data['current']['temp_c']);
    }

    /**
     * Maneja excepciones de peticiones HTTP
     */
    private function handleRequestException(RequestException $e, string $ciudad): void
    {
        $response = $e->getResponse();
        
        if ($response === null) {
            Logger::error("Error de conexión al obtener clima", [
                'ciudad' => $ciudad,
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException(
                'No se pudo conectar con el servicio meteorológico. Verifica tu conexión a internet.',
                0,
                $e
            );
        }

        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();

        Logger::error("Error HTTP al obtener clima", [
            'ciudad' => $ciudad,
            'status_code' => $statusCode,
            'response_body' => $responseBody,
            'error' => $e->getMessage()
        ]);

        // Manejar códigos de error específicos
        match ($statusCode) {
            400 => throw new \InvalidArgumentException(
                "Ciudad no encontrada: '{$ciudad}'. Verifica el nombre e inténtalo de nuevo."
            ),
            401 => throw new \RuntimeException(
                'Error de autenticación con el servicio meteorológico.'
            ),
            403 => throw new \RuntimeException(
                'Acceso denegado al servicio meteorológico.'
            ),
            429 => throw new \RuntimeException(
                'Demasiadas peticiones. Inténtalo más tarde.'
            ),
            500, 502, 503, 504 => throw new \RuntimeException(
                'El servicio meteorológico no está disponible temporalmente. Inténtalo más tarde.'
            ),
            default => throw new \RuntimeException(
                "Error del servicio meteorológico (código {$statusCode}). Inténtalo más tarde.",
                0,
                $e
            )
        };
    }

    /**
     * Valida la configuración del servicio
     */
    public function validateConfiguration(): array
    {
        $issues = [];

        if (empty($this->apiKey)) {
            $issues[] = 'WEATHER_API_KEY no está configurada';
        }

        if (empty($this->baseUrl)) {
            $issues[] = 'API_BASE_URL no está configurada';
        }

        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            $issues[] = 'API_BASE_URL no es una URL válida';
        }

        return $issues;
    }

    /**
     * Verifica la conectividad con la API
     */
    public function healthCheck(): array
    {
        try {
            // Intentar una petición simple
            $response = $this->httpClient->get($this->baseUrl, [
                'timeout' => 5,
                'connect_timeout' => 3
            ]);

            return [
                'status' => 'ok',
                'api_reachable' => true,
                'response_time' => null // Sería necesario medir el tiempo
            ];
        } catch (\Throwable $e) {
            Logger::warning("Health check falló", [
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'api_reachable' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
