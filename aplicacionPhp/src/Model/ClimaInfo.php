<?php

namespace App\Model;

/**
 * Modelo simplificado para la información del clima
 */
class ClimaInfo
{
    public function __construct(
        public readonly string $ciudad,
        public readonly string $region,
        public readonly string $pais,
        public readonly float $temperatura,
        public readonly string $descripcion,
        public readonly int $humedad,
        public readonly float $sensacionTermica,
        public readonly string $timestamp,
        public readonly ?string $icono = null,
        public readonly ?float $viento = null,
        public readonly ?string $direccionViento = null,
        public readonly ?float $presion = null,
        public readonly ?float $visibilidad = null,
        public readonly ?float $uv = null
    ) {}

    /**
     * Crea una instancia desde WeatherResponse
     */
    public static function fromWeatherResponse(WeatherResponse $weather): self
    {
        return new self(
            ciudad: $weather->location->name,
            region: $weather->location->region,
            pais: $weather->location->country,
            temperatura: $weather->current->tempC,
            descripcion: $weather->current->condition->text,
            humedad: $weather->current->humidity,
            sensacionTermica: $weather->current->feelslikeC,
            timestamp: date('c'),
            icono: $weather->current->condition->icon,
            viento: $weather->current->windKph,
            direccionViento: $weather->current->windDir,
            presion: $weather->current->pressureMb,
            visibilidad: $weather->current->visKm,
            uv: $weather->current->uv
        );
    }

    /**
     * Convierte a array para respuesta JSON
     */
    public function toArray(): array
    {
        $data = [
            'ciudad' => $this->ciudad,
            'region' => $this->region,
            'pais' => $this->pais,
            'temperatura' => $this->temperatura,
            'descripcion' => $this->descripcion,
            'humedad' => $this->humedad,
            'sensacion_termica' => $this->sensacionTermica,
            'timestamp' => $this->timestamp
        ];

        // Agregar campos opcionales si están presentes
        if ($this->icono !== null) {
            $data['icono'] = $this->icono;
        }

        if ($this->viento !== null) {
            $data['viento_kph'] = $this->viento;
        }

        if ($this->direccionViento !== null) {
            $data['direccion_viento'] = $this->direccionViento;
        }

        if ($this->presion !== null) {
            $data['presion_mb'] = $this->presion;
        }

        if ($this->visibilidad !== null) {
            $data['visibilidad_km'] = $this->visibilidad;
        }

        if ($this->uv !== null) {
            $data['indice_uv'] = $this->uv;
        }

        return $data;
    }

    /**
     * Convierte a formato para mostrar en la web
     */
    public function toDisplayArray(): array
    {
        return [
            'ubicacion' => "{$this->ciudad}, {$this->region}, {$this->pais}",
            'temperatura' => "{$this->temperatura}°C",
            'descripcion' => $this->descripcion,
            'humedad' => "{$this->humedad}%",
            'sensacion_termica' => "{$this->sensacionTermica}°C",
            'icono' => $this->icono,
            'detalles' => $this->getDetallesExtendidos()
        ];
    }

    /**
     * Obtiene detalles extendidos si están disponibles
     */
    private function getDetallesExtendidos(): array
    {
        $detalles = [];

        if ($this->viento !== null && $this->direccionViento !== null) {
            $detalles['viento'] = "{$this->viento} km/h {$this->direccionViento}";
        }

        if ($this->presion !== null) {
            $detalles['presion'] = "{$this->presion} mb";
        }

        if ($this->visibilidad !== null) {
            $detalles['visibilidad'] = "{$this->visibilidad} km";
        }

        if ($this->uv !== null) {
            $detalles['uv'] = $this->getUvDescription($this->uv);
        }

        return $detalles;
    }

    /**
     * Obtiene descripción del índice UV
     */
    private function getUvDescription(float $uv): string
    {
        $description = match (true) {
            $uv <= 2 => 'Bajo',
            $uv <= 5 => 'Moderado',
            $uv <= 7 => 'Alto',
            $uv <= 10 => 'Muy alto',
            default => 'Extremo'
        };

        return "{$uv} ({$description})";
    }

    /**
     * Valida que los datos básicos estén presentes
     */
    public function isValid(): bool
    {
        return !empty($this->ciudad) && 
               !empty($this->pais) && 
               !empty($this->descripcion);
    }
}
