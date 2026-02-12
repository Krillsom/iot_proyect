<?php

namespace App\Contexts\MqttIngestion\Domain\Queries;

final readonly class GetTriangulationDataQuery
{
    public function __construct(
        public int $hoursLimit = 24
    ) {}
}
