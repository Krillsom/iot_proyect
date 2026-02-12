<?php

namespace App\Contexts\MqttIngestion\Domain\Queries;

final readonly class GetReadingsByGatewayQuery
{
    public function __construct(
        public string $gatewayMac,
        public ?int $limit = 100
    ) {}
}
