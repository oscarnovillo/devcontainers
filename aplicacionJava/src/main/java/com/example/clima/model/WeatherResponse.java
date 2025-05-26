package com.example.clima.model;

import com.fasterxml.jackson.annotation.JsonProperty;

/**
 * Modelo para la respuesta del clima de WeatherAPI
 */
public record WeatherResponse(
        Location location,
        Current current
) {
    public record Location(
            String name,
            String region,
            String country,
            @JsonProperty("lat") double latitude,
            @JsonProperty("lon") double longitude,
            @JsonProperty("tz_id") String timeZone,
            @JsonProperty("localtime") String localTime
    ) {}

    public record Current(
            @JsonProperty("last_updated") String lastUpdated,
            @JsonProperty("temp_c") double temperatureCelsius,
            @JsonProperty("temp_f") double temperatureFahrenheit,
            @JsonProperty("is_day") int isDay,
            Condition condition,
            @JsonProperty("wind_mph") double windMph,
            @JsonProperty("wind_kph") double windKph,
            @JsonProperty("wind_degree") int windDegree,
            @JsonProperty("wind_dir") String windDirection,
            @JsonProperty("pressure_mb") double pressureMb,
            @JsonProperty("pressure_in") double pressureIn,
            @JsonProperty("precip_mm") double precipitationMm,
            @JsonProperty("precip_in") double precipitationIn,
            int humidity,
            int cloud,
            @JsonProperty("feelslike_c") double feelsLikeCelsius,
            @JsonProperty("feelslike_f") double feelsLikeFahrenheit,
            @JsonProperty("vis_km") double visibilityKm,
            @JsonProperty("vis_miles") double visibilityMiles,
            int uv
    ) {}

    public record Condition(
            String text,
            String icon,
            int code
    ) {}
}
