package com.example.clima.service;

import com.example.clima.config.WeatherProperties;
import com.example.clima.model.ClimaInfo;
import com.example.clima.model.WeatherResponse;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.web.reactive.function.client.WebClient;
import reactor.core.publisher.Mono;

/**
 * Servicio para obtener información del clima desde WeatherAPI
 */
@Service
public class ClimaService {
    
    private static final Logger logger = LoggerFactory.getLogger(ClimaService.class);
    
    private final WebClient webClient;
    private final WeatherProperties weatherProperties;
    
    public ClimaService(WebClient webClient, WeatherProperties weatherProperties) {
        this.webClient = webClient;
        this.weatherProperties = weatherProperties;
    }
    
    /**
     * Obtiene información del clima para una ciudad específica
     * 
     * @param ciudad Nombre de la ciudad
     * @return Mono con la información del clima
     */
    public Mono<ClimaInfo> obtenerClima(String ciudad) {
        if (weatherProperties.debug()) {
            logger.debug("Obteniendo clima para ciudad: {}", ciudad);
        }
        
        String url = String.format("%s/current.json?key=%s&q=%s&aqi=no",
                weatherProperties.baseUrl(),
                weatherProperties.apiKey(),
                ciudad);
        
        if (weatherProperties.debug()) {
            logger.debug("URL de consulta: {}", url.replaceAll("key=[^&]*", "key=***"));
        }
        
        return webClient.get()
                .uri(url)
                .retrieve()
                .bodyToMono(WeatherResponse.class)
                .map(ClimaInfo::fromWeatherResponse)
                .doOnSuccess(clima -> {
                    if (weatherProperties.debug()) {
                        logger.debug("Clima obtenido exitosamente para {}: {}°C", 
                                clima.ciudad(), clima.temperatura());
                    }
                })
                .doOnError(error -> {
                    logger.error("Error al obtener clima para {}: {}", ciudad, error.getMessage());
                });
    }
    
    /**
     * Verifica si el servicio está funcionando correctamente
     * 
     * @return Mono con el estado del servicio
     */
    public Mono<String> verificarEstado() {
        return obtenerClima("London")
                .map(clima -> "Servicio funcionando correctamente. Clima de prueba: " + 
                        clima.ciudad() + " - " + clima.temperatura() + "°C")
                .onErrorReturn("Servicio no disponible");
    }
}
