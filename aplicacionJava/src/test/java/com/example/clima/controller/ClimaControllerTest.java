package com.example.clima.controller;

import com.example.clima.model.ClimaInfo;
import com.example.clima.service.ClimaService;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.reactive.WebFluxTest;
import org.springframework.boot.test.mock.mockito.MockBean;
import org.springframework.test.web.reactive.server.WebTestClient;
import reactor.core.publisher.Mono;

import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.Mockito.when;

@WebFluxTest(ClimaController.class)
class ClimaControllerTest {

    @Autowired
    private WebTestClient webTestClient;

    @MockBean
    private ClimaService climaService;

    @Test
    void testObtenerClima() {
        // Given
        ClimaInfo climaMock = new ClimaInfo(
                "Madrid", "Spain", 20.0, "Sunny", 60, 10.0, "NW", "2025-05-25 10:00"
        );
        when(climaService.obtenerClima(anyString())).thenReturn(Mono.just(climaMock));

        // When & Then
        webTestClient.get()
                .uri("/api/clima?ciudad=Madrid")
                .exchange()
                .expectStatus().isOk()
                .expectBody(ClimaInfo.class)
                .isEqualTo(climaMock);
    }

    @Test
    void testObtenerClimaPorRuta() {
        // Given
        ClimaInfo climaMock = new ClimaInfo(
                "Barcelona", "Spain", 22.0, "Clear", 55, 8.0, "E", "2025-05-25 10:00"
        );
        when(climaService.obtenerClima(anyString())).thenReturn(Mono.just(climaMock));

        // When & Then
        webTestClient.get()
                .uri("/api/clima/Barcelona")
                .exchange()
                .expectStatus().isOk()
                .expectBody(ClimaInfo.class)
                .isEqualTo(climaMock);
    }

    @Test
    void testHealth() {
        // Given
        when(climaService.verificarEstado()).thenReturn(Mono.just("Servicio funcionando"));

        // When & Then
        webTestClient.get()
                .uri("/api/health")
                .exchange()
                .expectStatus().isOk()
                .expectBody()
                .jsonPath("$.status").isEqualTo("UP")
                .jsonPath("$.service").isEqualTo("Clima API Java");
    }

    @Test
    void testInfo() {
        // When & Then
        webTestClient.get()
                .uri("/api/")
                .exchange()
                .expectStatus().isOk()
                .expectBody()
                .jsonPath("$.service").isEqualTo("Clima API Java Spring Boot")
                .jsonPath("$.version").isEqualTo("1.0.0");
    }
}
