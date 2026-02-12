<?php

namespace App\Contexts\Device\Domain\Commands;

use App\Contexts\Device\Domain\Enums\DeviceType;
use App\Contexts\Device\Domain\Enums\SensorType;

final readonly class RegisterDeviceCommand
{
    public function __construct(
        public string $name,
        public DeviceType $type,
        public ?SensorType $sensorType,
        public string $macAddress,
        public ?string $ipAddress,
        public ?string $parentId,
        public ?array $metadata,
    ) {}
}
