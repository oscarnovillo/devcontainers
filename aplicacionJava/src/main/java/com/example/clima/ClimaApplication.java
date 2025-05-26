package com.example.clima;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.context.properties.ConfigurationPropertiesScan;

/**
 * Aplicación del clima en Java Spring Boot
 * Equivalente a las aplicaciones de Rust, Python y Node.js
 */
@SpringBootApplication
@ConfigurationPropertiesScan
public class ClimaApplication {

    public static void main(String[] args) {
        SpringApplication.run(ClimaApplication.class, args);
    }
}
