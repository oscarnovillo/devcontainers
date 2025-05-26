package com.example.clima;

import org.junit.jupiter.api.Test;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.test.context.TestPropertySource;

@SpringBootTest
@TestPropertySource(properties = {
    "weather.api-key=test-key",
    "weather.debug=true"
})
class ClimaApplicationTests {

    @Test
    void contextLoads() {
        // Test que verifica que el contexto de Spring se carga correctamente
    }
}
