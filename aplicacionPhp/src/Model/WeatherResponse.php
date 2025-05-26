<?php

namespace App\Model;

/**
 * Modelo que representa la respuesta de la API de WeatherAPI
 */
class WeatherResponse
{
    public function __construct(
        public readonly Location $location,
        public readonly CurrentWeather $current
    ) {}

    /**
     * Crea una instancia desde un array (respuesta de la API)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            Location::fromArray($data['location'] ?? []),
            CurrentWeather::fromArray($data['current'] ?? [])
        );
    }

    /**
     * Convierte a array
     */
    public function toArray(): array
    {
        return [
            'location' => $this->location->toArray(),
            'current' => $this->current->toArray()
        ];
    }
}

/**
 * Información de ubicación
 */
class Location
{
    public function __construct(
        public readonly string $name,
        public readonly string $region,
        public readonly string $country,
        public readonly float $lat = 0.0,
        public readonly float $lon = 0.0,
        public readonly string $tzId = '',
        public readonly int $localtimeEpoch = 0,
        public readonly string $localtime = ''
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            region: $data['region'] ?? '',
            country: $data['country'] ?? '',
            lat: (float) ($data['lat'] ?? 0.0),
            lon: (float) ($data['lon'] ?? 0.0),
            tzId: $data['tz_id'] ?? '',
            localtimeEpoch: (int) ($data['localtime_epoch'] ?? 0),
            localtime: $data['localtime'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'region' => $this->region,
            'country' => $this->country,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'tz_id' => $this->tzId,
            'localtime_epoch' => $this->localtimeEpoch,
            'localtime' => $this->localtime
        ];
    }
}

/**
 * Información meteorológica actual
 */
class CurrentWeather
{
    public function __construct(
        public readonly float $tempC,
        public readonly float $tempF,
        public readonly int $isDay,
        public readonly Condition $condition,
        public readonly float $windMph,
        public readonly float $windKph,
        public readonly int $windDegree,
        public readonly string $windDir,
        public readonly float $pressureMb,
        public readonly float $pressureIn,
        public readonly float $precipMm,
        public readonly float $precipIn,
        public readonly int $humidity,
        public readonly int $cloud,
        public readonly float $feelslikeC,
        public readonly float $feelslikeF,
        public readonly float $visKm,
        public readonly float $visMiles,
        public readonly float $uv,
        public readonly float $gustMph,
        public readonly float $gustKph
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tempC: (float) ($data['temp_c'] ?? 0.0),
            tempF: (float) ($data['temp_f'] ?? 0.0),
            isDay: (int) ($data['is_day'] ?? 0),
            condition: Condition::fromArray($data['condition'] ?? []),
            windMph: (float) ($data['wind_mph'] ?? 0.0),
            windKph: (float) ($data['wind_kph'] ?? 0.0),
            windDegree: (int) ($data['wind_degree'] ?? 0),
            windDir: $data['wind_dir'] ?? '',
            pressureMb: (float) ($data['pressure_mb'] ?? 0.0),
            pressureIn: (float) ($data['pressure_in'] ?? 0.0),
            precipMm: (float) ($data['precip_mm'] ?? 0.0),
            precipIn: (float) ($data['precip_in'] ?? 0.0),
            humidity: (int) ($data['humidity'] ?? 0),
            cloud: (int) ($data['cloud'] ?? 0),
            feelslikeC: (float) ($data['feelslike_c'] ?? 0.0),
            feelslikeF: (float) ($data['feelslike_f'] ?? 0.0),
            visKm: (float) ($data['vis_km'] ?? 0.0),
            visMiles: (float) ($data['vis_miles'] ?? 0.0),
            uv: (float) ($data['uv'] ?? 0.0),
            gustMph: (float) ($data['gust_mph'] ?? 0.0),
            gustKph: (float) ($data['gust_kph'] ?? 0.0)
        );
    }

    public function toArray(): array
    {
        return [
            'temp_c' => $this->tempC,
            'temp_f' => $this->tempF,
            'is_day' => $this->isDay,
            'condition' => $this->condition->toArray(),
            'wind_mph' => $this->windMph,
            'wind_kph' => $this->windKph,
            'wind_degree' => $this->windDegree,
            'wind_dir' => $this->windDir,
            'pressure_mb' => $this->pressureMb,
            'pressure_in' => $this->pressureIn,
            'precip_mm' => $this->precipMm,
            'precip_in' => $this->precipIn,
            'humidity' => $this->humidity,
            'cloud' => $this->cloud,
            'feelslike_c' => $this->feelslikeC,
            'feelslike_f' => $this->feelslikeF,
            'vis_km' => $this->visKm,
            'vis_miles' => $this->visMiles,
            'uv' => $this->uv,
            'gust_mph' => $this->gustMph,
            'gust_kph' => $this->gustKph
        ];
    }
}

/**
 * Condición meteorológica
 */
class Condition
{
    public function __construct(
        public readonly string $text,
        public readonly string $icon,
        public readonly int $code
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            text: $data['text'] ?? '',
            icon: $data['icon'] ?? '',
            code: (int) ($data['code'] ?? 0)
        );
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'icon' => $this->icon,
            'code' => $this->code
        ];
    }
}
