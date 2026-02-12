<?php

namespace App\Contexts\MqttIngestion\Application\Query;

use App\Contexts\MqttIngestion\Domain\Queries\GetDashboardStatsQuery;
use App\Contexts\MqttIngestion\Domain\Repositories\MqttReadingRepository;

class GetDashboardStatsQueryService
{
    public function __construct(
        private readonly MqttReadingRepository $repository
    ) {}

    public function execute(GetDashboardStatsQuery $query): array
    {
        return $this->repository->getDashboardStats();
    }
}
