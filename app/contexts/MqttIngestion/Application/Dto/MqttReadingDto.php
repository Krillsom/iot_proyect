<?php

namespace App\Contexts\MqttIngestion\Application\Dto;

final readonly class MqttReadingDto
{
    public function __construct(
        public int $id,
        public string $gatewayMac,
        public string $topic,
        public string $deviceMac,
        public string $deviceType,
        public ?string $deviceName,
        public ?int $rssi,
        public ?int $battery,
        public array $rawData,
        public string $dataTimestamp,
        public string $createdAt
    ) {}

    public static function fromModel($model): self
    {
        return new self(
            id: $model->id,
            gatewayMac: $model->gateway_mac,
            topic: $model->topic,
            deviceMac: $model->device_mac,
            deviceType: $model->device_type instanceof \BackedEnum 
                ? $model->device_type->value 
                : $model->device_type,
            deviceName: $model->device_name,
            rssi: $model->rssi,
            battery: $model->battery,
            rawData: $model->raw_data,
            dataTimestamp: $model->data_timestamp->toIso8601String(),
            createdAt: $model->created_at->toIso8601String()
        );
    }
}
