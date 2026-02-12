<?php

namespace App\Contexts\Device\Application\Dto;

use App\Contexts\Device\Domain\Enums\DeviceType;
use App\Contexts\Device\Domain\Enums\SensorType;

final readonly class RegisterDeviceDto
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

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            type: DeviceType::from($data['type']),
            sensorType: isset($data['sensor_type']) ? SensorType::from($data['sensor_type']) : null,
            macAddress: $data['mac_address'],
            ipAddress: $data['ip_address'] ?? null,
            parentId: $data['parent_id'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }
}
