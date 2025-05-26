package com.example.clima.config;

import org.springframework.boot.context.properties.ConfigurationProperties;
import org.springframework.validation.annotation.Validated;

import jakarta.validation.constraints.NotBlank;

/**
 * Propiedades de configuración para la aplicación del clima
 */
@ConfigurationProperties(prefix = "weather")
@Validated
public record WeatherProperties(
        @NotBlank String apiKey,
        String baseUrl,
        boolean debug,
        String logLevel
) {
    public WeatherProperties {
        // Valores por defecto
        if (baseUrl == null || baseUrl.isBlank()) {
            baseUrl = "http://api.weatherapi.com/v1";
        }
        if (logLevel == null || logLevel.isBlank()) {
            logLevel = "info";
        }
    }
}
