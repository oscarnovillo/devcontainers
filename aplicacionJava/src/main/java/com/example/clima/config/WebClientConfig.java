package com.example.clima.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.web.reactive.function.client.ExchangeStrategies;
import org.springframework.web.reactive.function.client.WebClient;

/**
 * Configuración de WebClient para llamadas HTTP
 */
@Configuration
public class WebClientConfig {

    @Bean
    public WebClient webClient() {
        // Configurar estrategias de intercambio con límites de memoria más altos
        ExchangeStrategies strategies = ExchangeStrategies.builder()
                .codecs(configurer -> configurer.defaultCodecs().maxInMemorySize(1024 * 1024))
                .build();

        return WebClient.builder()
                .exchangeStrategies(strategies)
                .build();
    }
}
