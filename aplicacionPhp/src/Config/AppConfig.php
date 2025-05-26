<?php

namespace App\Config;

/**
 * Configuración principal de la aplicación
 */
class AppConfig
{
    private static ?AppConfig $instance = null;
    private array $config;

    private function __construct()
    {
        $this->loadEnvironment();
        $this->initConfig();
    }

    public static function getInstance(): AppConfig
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function loadEnvironment(): void
    {
        // Cargar archivo .env adecuado según PHP_ENV
        $envMode = $_ENV['PHP_ENV'] ?? 'development';
        
        $envFile = match ($envMode) {
            'production' => '.env.production',
            'test' => '.env.test',
            default => '.env.development'
        };

        // Intentar cargar el archivo de entorno específico
        $envPath = dirname(__DIR__, 2) . '/' . $envFile;
        if (file_exists($envPath)) {
            $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2), $envFile);
            $dotenv->load();
        } else {
            // Si falla, intentar con el .env genérico
            $genericEnvPath = dirname(__DIR__, 2) . '/.env';
            if (file_exists($genericEnvPath)) {
                $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
                $dotenv->load();
            }
        }

        if ($this->isDebugEnabled()) {
            error_log("Modo de ejecución: {$envMode}");
            error_log("Archivo de entorno cargado: {$envFile}");
        }
    }

    private function initConfig(): void
    {
        $this->config = [
            'app' => [
                'name' => 'Clima App PHP',
                'version' => '1.0.0',
                'env' => $_ENV['PHP_ENV'] ?? 'development',
                'debug' => $this->parseBool($_ENV['DEBUG'] ?? 'false'),
                'timezone' => 'UTC'
            ],
            'api' => [
                'weather_api_key' => $_ENV['WEATHER_API_KEY'] ?? '',
                'base_url' => $_ENV['API_BASE_URL'] ?? 'https://api.weatherapi.com/v1',
                'timeout' => 30,
                'connect_timeout' => 10
            ],
            'logging' => [
                'level' => $_ENV['LOG_LEVEL'] ?? 'info',
                'path' => dirname(__DIR__, 2) . '/logs',
                'file' => 'clima_app.log'
            ]
        ];

        // Validar configuración crítica
        $this->validateConfig();
    }

    private function parseBool(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    private function validateConfig(): void
    {
        if (empty($this->config['api']['weather_api_key'])) {
            throw new \RuntimeException(
                'WEATHER_API_KEY no está configurada. ' .
                'Por favor, configura esta variable de entorno.'
            );
        }

        if (empty($this->config['api']['base_url'])) {
            throw new \RuntimeException('API_BASE_URL no puede estar vacía');
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function getWeatherApiKey(): string
    {
        return $this->get('api.weather_api_key');
    }

    public function getApiBaseUrl(): string
    {
        return $this->get('api.base_url');
    }

    public function getApiTimeout(): int
    {
        return $this->get('api.timeout');
    }

    public function getApiConnectTimeout(): int
    {
        return $this->get('api.connect_timeout');
    }

    public function isDebugEnabled(): bool
    {
        return $this->get('app.debug', false);
    }

    public function getEnvironment(): string
    {
        return $this->get('app.env');
    }

    public function getAppVersion(): string
    {
        return $this->get('app.version');
    }

    public function getAppName(): string
    {
        return $this->get('app.name');
    }

    public function isProduction(): bool
    {
        return $this->getEnvironment() === 'production';
    }

    public function isDevelopment(): bool
    {
        return $this->getEnvironment() === 'development';
    }

    public function isTest(): bool
    {
        return $this->getEnvironment() === 'test';
    }

    public function getLogLevel(): string
    {
        return $this->get('logging.level');
    }

    public function getLogPath(): string
    {
        return $this->get('logging.path');
    }

    public function getLogFile(): string
    {
        return $this->get('logging.file');
    }
}
