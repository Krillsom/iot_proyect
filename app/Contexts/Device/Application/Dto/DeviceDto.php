<?php

namespace App\Contexts\Device\Application\Dto;

use App\Contexts\Device\Domain\Device;
use App\Contexts\Device\Domain\Enums\DeviceStatus;
use App\Contexts\Device\Domain\Enums\DeviceType;
use App\Contexts\Device\Domain\Enums\SensorType;

final readonly class DeviceDto
{
    public function __construct(
        public string $id,
        public string $uuid,
        public string $name,
        public DeviceType $type,
        public ?SensorType $sensorType,
        public DeviceStatus $status,
        public string $macAddress,
        public ?string $ipAddress,
        public ?string $parentId,
        public ?array $metadata,
        public ?string $lastSeenAt,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromModel(Device $device): self
    {
        return new self(
            id: (string) $device->id,
            uuid: $device->uuid,
            name: $device->name,
            type: $device->type,
            sensorType: $device->sensor_type,
            status: $device->status,
            macAddress: $device->mac_address,
            ipAddress: $device->ip_address,
            parentId: $device->parent_id,
            metadata: $device->metadata,
            lastSeenAt: $device->last_seen_at?->toIso8601String(),
            createdAt: $device->created_at->toIso8601String(),
            updatedAt: $device->updated_at->toIso8601String(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'sensor_type' => $this->sensorType?->value,
            'sensor_type_label' => $this->sensorType?->label(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'mac_address' => $this->macAddress,
            'ip_address' => $this->ipAddress,
            'parent_id' => $this->parentId,
            'metadata' => $this->metadata,
            'last_seen_at' => $this->lastSeenAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
