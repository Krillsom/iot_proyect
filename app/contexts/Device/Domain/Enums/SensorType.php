<?php

namespace App\Contexts\Device\Domain\Enums;

enum SensorType: string
{
    case MOTION = 'motion';
    case GPS = 'gps';
    case TEMPERATURE = 'temperature';
    case HUMIDITY = 'humidity';
    case PRESSURE = 'pressure';
    case LIGHT = 'light';
    case SOUND = 'sound';
    case PROXIMITY = 'proximity';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::MOTION => 'Movimiento',
            self::GPS => 'GPS',
            self::TEMPERATURE => 'Temperatura',
            self::HUMIDITY => 'Humedad',
            self::PRESSURE => 'Presión',
            self::LIGHT => 'Luz',
            self::SOUND => 'Sonido',
            self::PROXIMITY => 'Proximidad',
            self::OTHER => 'Otro',
        };
    }

    public function unit(): ?string
    {
        return match($this) {
            self::TEMPERATURE => '°C',
            self::HUMIDITY => '%',
            self::PRESSURE => 'hPa',
            self::LIGHT => 'lux',
            self::SOUND => 'dB',
            self::PROXIMITY => 'cm',
            default => null,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
