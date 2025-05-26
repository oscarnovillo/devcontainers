<?php

/**
 * Bootstrap file para las pruebas PHPUnit
 */

// Configurar el entorno de pruebas
$_ENV['PHP_ENV'] = 'test';
$_ENV['WEATHER_API_KEY'] = 'test_api_key';
$_ENV['DEBUG'] = 'false';
$_ENV['LOG_LEVEL'] = 'error';

// Cargar autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Configurar timezone para pruebas
date_default_timezone_set('UTC');

// Configurar manejo de errores para pruebas
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Bootstrap de pruebas cargado correctamente\n";
