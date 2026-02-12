<?php

namespace App\Contexts\MqttIngestion\Domain\Queries;

final readonly class GetDashboardStatsQuery
{
    public function __construct(
        public ?int $hoursForActivity = 24
    ) {}
}
