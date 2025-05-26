<?php

namespace App\Utils;

/**
 * Utilidad para manejar respuestas HTTP
 */
class Response
{
    /**
     * Envía una respuesta JSON
     */
    public static function json(mixed $data, int $status = 200, array $headers = []): void
    {
        // Configurar headers por defecto
        $defaultHeaders = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
        ];

        $headers = array_merge($defaultHeaders, $headers);

        // Establecer código de estado
        http_response_code($status);

        // Enviar headers
        foreach ($headers as $key => $value) {
            header("{$key}: {$value}");
        }

        // Enviar datos JSON
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Envía una respuesta de éxito
     */
    public static function success(mixed $data, string $message = 'Success', int $status = 200): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ], $status);
    }

    /**
     * Envía una respuesta de error
     */
    public static function error(string $message, int $status = 500, ?string $code = null, mixed $details = null): void
    {
        $error = [
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $code,
                'status' => $status
            ],
            'timestamp' => date('c')
        ];

        if ($details !== null) {
            $error['error']['details'] = $details;
        }

        self::json($error, $status);
    }

    /**
     * Envía una respuesta 404
     */
    public static function notFound(string $message = 'Resource not found'): void
    {
        self::error($message, 404, 'NOT_FOUND');
    }

    /**
     * Envía una respuesta 400
     */
    public static function badRequest(string $message = 'Bad request', mixed $details = null): void
    {
        self::error($message, 400, 'BAD_REQUEST', $details);
    }

    /**
     * Envía una respuesta 401
     */
    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::error($message, 401, 'UNAUTHORIZED');
    }

    /**
     * Envía una respuesta 500
     */
    public static function internalServerError(string $message = 'Internal server error'): void
    {
        self::error($message, 500, 'INTERNAL_SERVER_ERROR');
    }

    /**
     * Envía una respuesta de validación
     */
    public static function validationError(array $errors): void
    {
        self::error('Validation failed', 422, 'VALIDATION_ERROR', $errors);
    }

    /**
     * Envía una respuesta de salud del sistema
     */
    public static function health(string $status = 'ok', array $data = []): void
    {
        $response = [
            'status' => $status,
            'timestamp' => date('c'),
            'version' => \App\Config\AppConfig::getInstance()->getAppVersion()
        ];

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        self::json($response);
    }

    /**
     * Maneja respuestas OPTIONS para CORS
     */
    public static function options(): void
    {
        self::json(['message' => 'Options handled'], 200);
    }
}
