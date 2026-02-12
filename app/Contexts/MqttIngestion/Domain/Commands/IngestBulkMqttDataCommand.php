<?php

namespace App\Contexts\MqttIngestion\Domain\Commands;

final readonly class IngestBulkMqttDataCommand
{
    public function __construct(
        public string $topic,
        public array $payloads, // Array de múltiples lecturas
        public string $gatewayMac
    ) {}
}
