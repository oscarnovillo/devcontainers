<?php

namespace Tests\Unit;

use App\Service\ClimaService;
use App\Model\ClimaInfo;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class ClimaServiceTest extends TestCase
{
    private ClimaService $climaService;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar variables de entorno para pruebas
        $_ENV['WEATHER_API_KEY'] = 'test_api_key';
        $_ENV['API_BASE_URL'] = 'https://api.weatherapi.com/v1';
        $_ENV['PHP_ENV'] = 'test';
        
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        
        // Crear servicio con cliente HTTP mockeado
        $this->climaService = new ClimaService();
        
        // Usar reflexión para inyectar el cliente mockeado
        $reflection = new \ReflectionClass($this->climaService);
        $httpClientProperty = $reflection->getProperty('httpClient');
        $httpClientProperty->setAccessible(true);
        $httpClientProperty->setValue($this->climaService, new Client(['handler' => $handlerStack]));
    }

    public function testObtenerClimaExitoso(): void
    {
        // Arranjar - Preparar respuesta mock
        $responseData = [
            'location' => [
                'name' => 'Madrid',
                'region' => 'Madrid',
                'country' => 'Spain',
                'lat' => 40.4,
                'lon' => -3.68,
                'tz_id' => 'Europe/Madrid',
                'localtime_epoch' => 1640995200,
                'localtime' => '2022-01-01 12:00'
            ],
            'current' => [
                'temp_c' => 15.0,
                'temp_f' => 59.0,
                'is_day' => 1,
                'condition' => [
                    'text' => 'Soleado',
                    'icon' => '//cdn.weatherapi.com/weather/64x64/day/113.png',
                    'code' => 1000
                ],
                'wind_mph' => 6.9,
                'wind_kph' => 11.2,
                'wind_degree' => 230,
                'wind_dir' => 'SW',
                'pressure_mb' => 1013.0,
                'pressure_in' => 29.91,
                'precip_mm' => 0.0,
                'precip_in' => 0.0,
                'humidity' => 45,
                'cloud' => 0,
                'feelslike_c' => 15.0,
                'feelslike_f' => 59.0,
                'vis_km' => 10.0,
                'vis_miles' => 6.0,
                'uv' => 5.0,
                'gust_mph' => 8.1,
                'gust_kph' => 13.0
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($responseData)));

        // Actuar
        $resultado = $this->climaService->obtenerClima('Madrid');

        // Afirmar
        $this->assertInstanceOf(ClimaInfo::class, $resultado);
        $this->assertEquals('Madrid', $resultado->ciudad);
        $this->assertEquals('Madrid', $resultado->region);
        $this->assertEquals('Spain', $resultado->pais);
        $this->assertEquals(15.0, $resultado->temperatura);
        $this->assertEquals('Soleado', $resultado->descripcion);
        $this->assertEquals(45, $resultado->humedad);
        $this->assertEquals(15.0, $resultado->sensacionTermica);
        $this->assertTrue($resultado->isValid());
    }

    public function testObtenerClimaConCiudadVacia(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La ciudad no puede estar vacía');

        $this->climaService->obtenerClima('');
    }

    public function testObtenerClimaConCiudadSoloEspacios(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La ciudad no puede estar vacía');

        $this->climaService->obtenerClima('   ');
    }

    public function testObtenerClimaCiudadNoEncontrada(): void
    {
        // Simular respuesta 400 (ciudad no encontrada)
        $this->mockHandler->append(new RequestException(
            'Bad Request',
            new Request('GET', 'test'),
            new Response(400, [], json_encode(['error' => ['message' => 'No matching location found.']]))
        ));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Ciudad no encontrada/');

        $this->climaService->obtenerClima('CiudadInexistente123');
    }

    public function testObtenerClimaErrorAutenticacion(): void
    {
        // Simular respuesta 401 (error de autenticación)
        $this->mockHandler->append(new RequestException(
            'Unauthorized',
            new Request('GET', 'test'),
            new Response(401, [], json_encode(['error' => ['message' => 'API key not provided.']]))
        ));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Error de autenticación/');

        $this->climaService->obtenerClima('Madrid');
    }

    public function testObtenerClimaTooManyRequests(): void
    {
        // Simular respuesta 429 (demasiadas peticiones)
        $this->mockHandler->append(new RequestException(
            'Too Many Requests',
            new Request('GET', 'test'),
            new Response(429, [], json_encode(['error' => ['message' => 'API call quota exceeded.']]))
        ));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Demasiadas peticiones/');

        $this->climaService->obtenerClima('Madrid');
    }

    public function testObtenerClimaErrorServidor(): void
    {
        // Simular respuesta 500 (error del servidor)
        $this->mockHandler->append(new RequestException(
            'Internal Server Error',
            new Request('GET', 'test'),
            new Response(500, [], json_encode(['error' => ['message' => 'Internal application error.']]))
        ));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/servicio meteorológico no está disponible/');

        $this->climaService->obtenerClima('Madrid');
    }

    public function testObtenerClimaErrorConexion(): void
    {
        // Simular error de conexión
        $this->mockHandler->append(new RequestException(
            'Connection error',
            new Request('GET', 'test')
        ));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/No se pudo conectar/');

        $this->climaService->obtenerClima('Madrid');
    }

    public function testObtenerClimaRespuestaJsonInvalida(): void
    {
        // Simular respuesta con JSON inválido
        $this->mockHandler->append(new Response(200, [], 'invalid json'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Error al decodificar la respuesta JSON/');

        $this->climaService->obtenerClima('Madrid');
    }

    public function testObtenerClimaRespuestaFormatoInvalido(): void
    {
        // Simular respuesta con formato incorrecto
        $this->mockHandler->append(new Response(200, [], json_encode(['invalid' => 'response'])));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Respuesta de la API en formato inválido/');

        $this->climaService->obtenerClima('Madrid');
    }

    public function testValidateConfiguration(): void
    {
        // Test con configuración válida
        $issues = $this->climaService->validateConfiguration();
        $this->assertEmpty($issues);

        // Test con configuración inválida
        $_ENV['WEATHER_API_KEY'] = '';
        $climaService = new ClimaService();
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/WEATHER_API_KEY no está configurada/');
    }

    public function testHealthCheckExitoso(): void
    {
        // Simular respuesta exitosa para health check
        $this->mockHandler->append(new Response(200, [], json_encode(['status' => 'ok'])));

        $result = $this->climaService->healthCheck();

        $this->assertEquals('ok', $result['status']);
        $this->assertTrue($result['api_reachable']);
    }

    public function testHealthCheckError(): void
    {
        // Simular error en health check
        $this->mockHandler->append(new RequestException(
            'Connection timeout',
            new Request('GET', 'test')
        ));

        $result = $this->climaService->healthCheck();

        $this->assertEquals('error', $result['status']);
        $this->assertFalse($result['api_reachable']);
        $this->assertArrayHasKey('error', $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Limpiar variables de entorno
        unset($_ENV['WEATHER_API_KEY']);
        unset($_ENV['API_BASE_URL']);
        unset($_ENV['PHP_ENV']);
    }
}
