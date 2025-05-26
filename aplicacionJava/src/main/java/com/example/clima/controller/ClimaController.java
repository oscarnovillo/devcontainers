package com.example.clima.controller;

import com.example.clima.model.ClimaInfo;
import com.example.clima.service.ClimaService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import reactor.core.publisher.Mono;

import java.util.Map;

/**
 * Controlador REST para la API del clima
 */
@RestController
@RequestMapping("/api")
@CrossOrigin(origins = "*")
public class ClimaController {
    
    private static final Logger logger = LoggerFactory.getLogger(ClimaController.class);
    
    private final ClimaService climaService;
    
    public ClimaController(ClimaService climaService) {
        this.climaService = climaService;
    }
    
    /**
     * Endpoint principal para obtener información del clima
     * 
     * @param ciudad Nombre de la ciudad (parámetro de consulta)
     * @return ResponseEntity con la información del clima
     */
    @GetMapping("/clima")
    public Mono<ResponseEntity<ClimaInfo>> obtenerClima(@RequestParam String ciudad) {
        logger.info("Solicitud de clima para ciudad: {}", ciudad);
        
        if (ciudad == null || ciudad.trim().isEmpty()) {
            return Mono.just(ResponseEntity.badRequest().build());
        }
        
        return climaService.obtenerClima(ciudad.trim())
                .map(clima -> ResponseEntity.ok(clima))
                .onErrorReturn(ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).build());
    }
    
    /**
     * Endpoint alternativo usando path variable
     * 
     * @param ciudad Nombre de la ciudad (variable de ruta)
     * @return ResponseEntity con la información del clima
     */
    @GetMapping("/clima/{ciudad}")
    public Mono<ResponseEntity<ClimaInfo>> obtenerClimaPorRuta(@PathVariable String ciudad) {
        logger.info("Solicitud de clima por ruta para ciudad: {}", ciudad);
        
        return climaService.obtenerClima(ciudad)
                .map(clima -> ResponseEntity.ok(clima))
                .onErrorReturn(ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).build());
    }
    
    /**
     * Endpoint de estado/salud de la aplicación
     * 
     * @return ResponseEntity con el estado del servicio
     */
    @GetMapping("/health")
    public Mono<ResponseEntity<Map<String, Object>>> health() {
        return climaService.verificarEstado()
                .map(estado -> {
                    Map<String, Object> response = Map.of(
                            "status", "UP",
                            "service", "Clima API Java",
                            "message", estado,
                            "timestamp", System.currentTimeMillis()
                    );
                    return ResponseEntity.ok(response);
                })
                .onErrorReturn(ResponseEntity.status(HttpStatus.SERVICE_UNAVAILABLE)
                        .body(Map.of(
                                "status", "DOWN",
                                "service", "Clima API Java",
                                "message", "Servicio no disponible",
                                "timestamp", System.currentTimeMillis()
                        )));
    }
    
    /**
     * Endpoint raíz con información de la API
     * 
     * @return ResponseEntity con información básica
     */
    @GetMapping("/")
    public ResponseEntity<Map<String, String>> info() {
        Map<String, String> info = Map.of(
                "service", "Clima API Java Spring Boot",
                "version", "1.0.0",
                "endpoints", "/api/clima?ciudad=Madrid, /api/clima/{ciudad}, /api/health",
                "description", "API del clima equivalente a Rust, Python y Node.js"
        );
        return ResponseEntity.ok(info);
    }
}
