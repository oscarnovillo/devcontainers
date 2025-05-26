package com.example.clima.model;

/**
 * DTO simplificado para la respuesta del clima
 */
public record ClimaInfo(
        String ciudad,
        String pais,
        double temperatura,
        String descripcion,
        int humedad,
        double viento,
        String direccionViento,
        String fechaActualizacion
) {
    /**
     * Convierte una respuesta de WeatherAPI a ClimaInfo
     */
    public static ClimaInfo fromWeatherResponse(WeatherResponse response) {
        return new ClimaInfo(
                response.location().name(),
                response.location().country(),
                response.current().temperatureCelsius(),
                response.current().condition().text(),
                response.current().humidity(),
                response.current().windKph(),
                response.current().windDirection(),
                response.current().lastUpdated()
        );
    }
}
