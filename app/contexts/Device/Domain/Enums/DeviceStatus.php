<?php

namespace App\Contexts\Device\Domain\Enums;

enum DeviceStatus: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case MAINTENANCE = 'maintenance';
    case ERROR = 'error';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::ONLINE => 'En línea',
            self::OFFLINE => 'Fuera de línea',
            self::MAINTENANCE => 'En mantenimiento',
            self::ERROR => 'Con error',
            self::INACTIVE => 'Inactivo',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ONLINE => 'success',
            self::OFFLINE => 'secondary',
            self::MAINTENANCE => 'warning',
            self::ERROR => 'danger',
            self::INACTIVE => 'gray',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
