<?php

namespace App\Contexts\MqttIngestion\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MqttDataReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $topic,
        public readonly array $data,
        public readonly string $gatewayMac,
        public readonly \DateTimeImmutable $occurredAt
    ) {}
}
