<?php

namespace Tests\Unit;

use App\Controller\ClimaController;
use App\Service\ClimaService;
use App\Model\ClimaInfo;
use PHPUnit\Framework\TestCase;

class ClimaControllerTest extends TestCase
{
    private ClimaController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar variables de entorno para pruebas
        $_ENV['PHP_ENV'] = 'test';
        $_ENV['WEATHER_API_KEY'] = 'test_api_key';
        $_ENV['DEBUG'] = 'true';
        
        $this->controller = new ClimaController();
        
        // Limpiar variables de servidor
        $_SERVER = [];
    }

    public function testHandleRequestHealthEndpoint(): void
    {
        // Configurar request para health endpoint
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/health';
        
        // Capturar output
        ob_start();
        $this->controller->handleRequest();
        $output = ob_get_clean();
        
        // Verificar que se devuelve JSON válido
        $this->assertJson($output);
        
        $data = json_decode($output, true);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('timestamp', $data);
        $this->assertArrayHasKey('version', $data);
    }

    public function testHandleRequestClimaGetEndpoint(): void
    {
        // Configurar request para clima endpoint
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/clima/Madrid';
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit Test';
        
        // Mock del servicio de clima sería necesario para una prueba completa
        // Por ahora verificamos que el método no falle
        ob_start();
        
        try {
            $this->controller->handleRequest();
        } catch (\Throwable $e) {
            // Esperamos una excepción debido a la configuración de prueba
            $this->assertStringContainsString('WEATHER_API_KEY', $e->getMessage());
        }
        
        $output = ob_get_clean();
        
        // Si hay output, debe ser JSON válido
        if (!empty($output)) {
            $this->assertJson($output);
        }
    }

    public function testHandleRequestOptionsMethod(): void
    {
        // Configurar request OPTIONS para CORS
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $_SERVER['REQUEST_URI'] = '/api/clima';
        
        ob_start();
        $this->controller->handleRequest();
        $output = ob_get_clean();
        
        $this->assertJson($output);
        
        $data = json_decode($output, true);
        $this->assertEquals('Options handled', $data['message']);
    }

    public function testHandleRequestNotFoundEndpoint(): void
    {
        // Configurar request para endpoint inexistente
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/inexistente';
        
        ob_start();
        $this->controller->handleRequest();
        $output = ob_get_clean();
        
        $this->assertJson($output);
        
        $data = json_decode($output, true);
        $this->assertFalse($data['success']);
        $this->assertEquals(404, $data['error']['status']);
        $this->assertStringContainsString('Endpoint no encontrado', $data['error']['message']);
    }

    public function testHandleRequestPostWithEmptyBody(): void
    {
        // Configurar request POST sin cuerpo
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/clima';
        
        // Mock de php://input vacío
        $this->mockPhpInput('');
        
        ob_start();
        $this->controller->handleRequest();
        $output = ob_get_clean();
        
        $this->assertJson($output);
        
        $data = json_decode($output, true);
        $this->assertFalse($data['success']);
        $this->assertEquals(400, $data['error']['status']);
    }

    public function testHandleRequestPostWithInvalidJson(): void
    {
        // Configurar request POST con JSON inválido
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/clima';
        
        // Mock de php://input con JSON inválido
        $this->mockPhpInput('invalid json');
        
        ob_start();
        $this->controller->handleRequest();
        $output = ob_get_clean();
        
        $this->assertJson($output);
        
        $data = json_decode($output, true);
        $this->assertFalse($data['success']);
        $this->assertEquals(400, $data['error']['status']);
        $this->assertStringContainsString('JSON inválido', $data['error']['message']);
    }

    public function testHandleRequestPostWithMissingCiudad(): void
    {
        // Configurar request POST sin campo ciudad
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/clima';
        
        // Mock de php://input con JSON válido pero sin ciudad
        $this->mockPhpInput(json_encode(['other_field' => 'value']));
        
        ob_start();
        $this->controller->handleRequest();
        $output = ob_get_clean();
        
        $this->assertJson($output);
        
        $data = json_decode($output, true);
        $this->assertFalse($data['success']);
        $this->assertEquals(400, $data['error']['status']);
        $this->assertStringContainsString('Campo "ciudad" requerido', $data['error']['message']);
    }

    public function testHandleRequestMethodNotAllowed(): void
    {
        // Configurar request con método no permitido
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $_SERVER['REQUEST_URI'] = '/api/clima/Madrid';
        
        ob_start();
        $this->controller->handleRequest();
        $output = ob_get_clean();
        
        $this->assertJson($output);
        
        $data = json_decode($output, true);
        $this->assertFalse($data['success']);
        $this->assertEquals(405, $data['error']['status']);
        $this->assertStringContainsString('Método no permitido', $data['error']['message']);
    }

    public function testSanitizeCiudad(): void
    {
        // Usar reflexión para acceder al método privado
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('sanitizeCiudad');
        $method->setAccessible(true);
        
        // Test casos normales
        $this->assertEquals('Madrid', $method->invoke($this->controller, 'Madrid'));
        $this->assertEquals('Barcelona', $method->invoke($this->controller, '  Barcelona  '));
        
        // Test sanitización de caracteres peligrosos
        $this->assertEquals('Madrid', $method->invoke($this->controller, 'Madrid<script>'));
        $this->assertEquals('New York', $method->invoke($this->controller, 'New"York'));
    }

    public function testIsValidCiudad(): void
    {
        // Usar reflexión para acceder al método privado
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('isValidCiudad');
        $method->setAccessible(true);
        
        // Test casos válidos
        $this->assertTrue($method->invoke($this->controller, 'Madrid'));
        $this->assertTrue($method->invoke($this->controller, 'New York'));
        $this->assertTrue($method->invoke($this->controller, 'São Paulo'));
        
        // Test casos inválidos
        $this->assertFalse($method->invoke($this->controller, ''));
        $this->assertFalse($method->invoke($this->controller, '   '));
        $this->assertFalse($method->invoke($this->controller, str_repeat('a', 101))); // Demasiado largo
        $this->assertFalse($method->invoke($this->controller, '---'));
    }

    public function testDebugEndpointInDevelopment(): void
    {
        // Configurar entorno de desarrollo
        $_ENV['PHP_ENV'] = 'development';
        
        $controller = new ClimaController();
        
        ob_start();
        $controller->debug();
        $output = ob_get_clean();
        
        $this->assertJson($output);
        
        $data = json_decode($output, true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('config', $data['data']);
        $this->assertArrayHasKey('service', $data['data']);
        $this->assertArrayHasKey('request', $data['data']);
    }

    public function testDebugEndpointInProduction(): void
    {
        // Configurar entorno de producción
        $_ENV['PHP_ENV'] = 'production';
        
        $controller = new ClimaController();
        
        ob_start();
        $controller->debug();
        $output = ob_get_clean();
        
        $this->assertJson($output);
        
        $data = json_decode($output, true);
        $this->assertFalse($data['success']);
        $this->assertEquals(404, $data['error']['status']);
    }

    /**
     * Mock del contenido de php://input
     */
    private function mockPhpInput(string $content): void
    {
        // En un entorno real, esto requeriría una biblioteca de mocking más sofisticada
        // o el uso de streams personalizados
        if (function_exists('stream_wrapper_register')) {
            stream_wrapper_unregister('php');
            stream_wrapper_register('php', MockInputStreamWrapper::class);
            MockInputStreamWrapper::$content = $content;
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Limpiar variables de entorno
        unset($_ENV['PHP_ENV']);
        unset($_ENV['WEATHER_API_KEY']);
        unset($_ENV['DEBUG']);
        
        // Restaurar stream wrapper si fue modificado
        if (function_exists('stream_wrapper_restore')) {
            stream_wrapper_restore('php');
        }
    }
}

/**
 * Mock stream wrapper para simular php://input
 */
class MockInputStreamWrapper
{
    public static string $content = '';
    private int $position = 0;

    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool
    {
        $this->position = 0;
        return true;
    }

    public function stream_read(int $count): string|false
    {
        $ret = substr(self::$content, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    public function stream_eof(): bool
    {
        return $this->position >= strlen(self::$content);
    }

    public function stream_stat(): array|false
    {
        return [
            'size' => strlen(self::$content)
        ];
    }
}
