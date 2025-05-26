<?php

namespace App\Utils;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use App\Config\AppConfig;

/**
 * Utilidad para logging centralizado
 */
class Logger
{
    private static ?MonologLogger $instance = null;
    private static AppConfig $config;

    public static function getInstance(): MonologLogger
    {
        if (self::$instance === null) {
            self::$config = AppConfig::getInstance();
            self::$instance = self::createLogger();
        }

        return self::$instance;
    }

    private static function createLogger(): MonologLogger
    {
        $logger = new MonologLogger(self::$config->getAppName());

        // Configurar handler según el entorno
        if (self::$config->isDevelopment()) {
            // En desarrollo, log a stderr y archivo
            $logger->pushHandler(new StreamHandler('php://stderr', self::getLogLevel()));
            
            // También a archivo si existe el directorio
            if (self::ensureLogDirectory()) {
                $logFile = self::$config->getLogPath() . '/' . self::$config->getLogFile();
                $logger->pushHandler(new RotatingFileHandler($logFile, 0, self::getLogLevel()));
            }
        } else {
            // En producción, solo a archivo
            if (self::ensureLogDirectory()) {
                $logFile = self::$config->getLogPath() . '/' . self::$config->getLogFile();
                $logger->pushHandler(new RotatingFileHandler($logFile, 30, self::getLogLevel()));
            } else {
                // Fallback a stderr si no se puede escribir archivo
                $logger->pushHandler(new StreamHandler('php://stderr', self::getLogLevel()));
            }
        }

        // Configurar formato
        foreach ($logger->getHandlers() as $handler) {
            $formatter = new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                'Y-m-d H:i:s',
                true,
                true
            );
            $handler->setFormatter($formatter);
        }

        return $logger;
    }

    private static function getLogLevel(): int
    {
        $level = strtolower(self::$config->getLogLevel());
        
        return match ($level) {
            'debug' => MonologLogger::DEBUG,
            'info' => MonologLogger::INFO,
            'notice' => MonologLogger::NOTICE,
            'warning' => MonologLogger::WARNING,
            'error' => MonologLogger::ERROR,
            'critical' => MonologLogger::CRITICAL,
            'alert' => MonologLogger::ALERT,
            'emergency' => MonologLogger::EMERGENCY,
            default => MonologLogger::INFO
        };
    }

    private static function ensureLogDirectory(): bool
    {
        $logPath = self::$config->getLogPath();
        
        if (!is_dir($logPath)) {
            try {
                mkdir($logPath, 0755, true);
                return true;
            } catch (\Throwable $e) {
                error_log("No se pudo crear el directorio de logs: {$logPath} - " . $e->getMessage());
                return false;
            }
        }

        return is_writable($logPath);
    }

    // Métodos de conveniencia para logging
    public static function debug(string $message, array $context = []): void
    {
        self::getInstance()->debug($message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::getInstance()->info($message, $context);
    }

    public static function notice(string $message, array $context = []): void
    {
        self::getInstance()->notice($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::getInstance()->warning($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::getInstance()->error($message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        self::getInstance()->critical($message, $context);
    }

    public static function alert(string $message, array $context = []): void
    {
        self::getInstance()->alert($message, $context);
    }

    public static function emergency(string $message, array $context = []): void
    {
        self::getInstance()->emergency($message, $context);
    }
}
