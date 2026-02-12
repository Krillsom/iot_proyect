<?php

namespace App\Contexts\MqttIngestion\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GatewayStatusUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $gatewayMac,
        public readonly int $freeMemory,
        public readonly float $load,
        public readonly \DateTimeImmutable $occurredAt
    ) {}
}
