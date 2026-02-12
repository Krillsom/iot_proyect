<?php

namespace App\Contexts\MqttIngestion\Domain\Commands;

final readonly class IngestMqttDataCommand
{
    public function __construct(
        public string $topic,
        public array $payload,
        public string $gatewayMac
    ) {}
}
