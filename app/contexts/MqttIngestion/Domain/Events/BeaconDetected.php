<?php

namespace App\Contexts\MqttIngestion\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BeaconDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $beaconMac,
        public readonly string $gatewayMac,
        public readonly int $rssi,
        public readonly \DateTimeImmutable $occurredAt
    ) {}
}
