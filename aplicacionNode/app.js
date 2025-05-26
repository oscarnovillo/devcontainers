#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const readline = require('readline');
const axios = require('axios');

/**
 * Cargar variables de entorno desde el archivo correspondiente
 */
function cargarConfiguracion() {
    // Determinar el modo de ejecución
    const envMode = process.env.NODE_ENV || 'development';
    
    // Seleccionar el archivo .env correspondiente
    const envFile = envMode === 'production' ? '.env.production' : '.env.development';
    
    console.log(`Modo de ejecución: ${envMode}`);
    
    // Cargar variables del archivo .env (si existe)
    try {
        require('dotenv').config({ path: envFile });
    } catch (error) {
        // Si dotenv no está disponible o falla, intentar con .env genérico
        try {
            require('dotenv').config();
        } catch (err) {
            // Continuar sin dotenv si no está instalado
        }
    }
    
    // Mostrar información de depuración si está habilitado
    if (process.env.DEBUG === 'true') {
        console.log('Modo de depuración activado');
        console.log(`Nivel de log: ${process.env.LOG_LEVEL || 'info'}`);
    }
}

/**
 * Obtener información del clima para una ciudad específica
 * @param {string} ciudad - Nombre de la ciudad
 * @returns {Promise<Object>} Datos del clima
 */
async function obtenerClima(ciudad) {
    // Obtener la clave API desde las variables de entorno
    const apiKey = process.env.WEATHER_API_KEY;
    if (!apiKey) {
        throw new Error('No se encontró la variable WEATHER_API_KEY');
    }
    
    const url = `https://api.weatherapi.com/v1/current.json?key=${apiKey}&q=${ciudad}&lang=es`;
    
    // Mostrar la URL en modo depuración
    if (process.env.DEBUG === 'true') {
        console.log(`DEBUG: Consultando URL: ${url}`);
    }
    
    try {
        const response = await axios.get(url, { timeout: 10000 });
        return response.data;
    } catch (error) {
        if (error.response) {
            throw new Error(`Error de la API: ${error.response.status} - ${error.response.data.error?.message || 'Error desconocido'}`);
        } else if (error.request) {
            throw new Error('Error de conexión: No se pudo conectar con la API');
        } else {
            throw new Error(`Error al consultar la API: ${error.message}`);
        }
    }
}

/**
 * Mostrar la información del clima en formato legible
 * @param {Object} tiempo - Datos del clima
 */
function mostrarClima(tiempo) {
    const { location, current } = tiempo;
    
    console.log(`\nClima actual en ${location.name}:`);
    console.log(`País: ${location.country}`);
    console.log(`Región: ${location.region}`);
    console.log(`Temperatura: ${current.temp_c.toFixed(1)}°C`);
    console.log(`Sensación térmica: ${current.feelslike_c.toFixed(1)}°C`);
    console.log(`Humedad: ${current.humidity}%`);
    console.log(`Condición: ${current.condition.text}`);
}

/**
 * Función principal de la aplicación
 */
async function main() {
    // Cargar variables de entorno
    cargarConfiguracion();
    
    console.log('Aplicación de Clima');
    console.log('-----------------');
    
    // Crear interfaz de lectura
    const rl = readline.createInterface({
        input: process.stdin,
        output: process.stdout
    });
    
    try {
        const ciudad = await new Promise((resolve) => {
            rl.question('Ingresa el nombre de una ciudad: ', resolve);
        });
        
        if (!ciudad.trim()) {
            console.log('Error: Debes ingresar el nombre de una ciudad');
            return;
        }
        
        const tiempo = await obtenerClima(ciudad.trim());
        mostrarClima(tiempo);
        
    } catch (error) {
        console.log(`Error: ${error.message}`);
    } finally {
        rl.close();
    }
}

// Manejo de señales para cierre graceful
process.on('SIGINT', () => {
    console.log('\nAplicación interrumpida por el usuario');
    process.exit(0);
});

process.on('SIGTERM', () => {
    console.log('\nAplicación terminada');
    process.exit(0);
});

// Ejecutar la aplicación
if (require.main === module) {
    main().catch((error) => {
        console.error(`Error fatal: ${error.message}`);
        process.exit(1);
    });
}

module.exports = {
    cargarConfiguracion,
    obtenerClima,
    mostrarClima,
    main
};
