<?php

namespace App\Contexts\Device\Domain\Enums;

enum DeviceType: string
{
    case SENSOR = 'sensor';
    case CAMERA = 'camera';
    case GATEWAY = 'gateway';
    case EDGE = 'edge';

    public function label(): string
    {
        return match($this) {
            self::SENSOR => 'Sensor',
            self::CAMERA => 'Cámara',
            self::GATEWAY => 'Gateway',
            self::EDGE => 'Edge Device',
        };
    }

    public function canHaveChildren(): bool
    {
        return match($this) {
            self::EDGE, self::GATEWAY => true,
            self::SENSOR, self::CAMERA => false,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
