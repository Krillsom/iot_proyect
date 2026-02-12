<?php

namespace App\Contexts\MqttIngestion\Application\Query;

use App\Contexts\MqttIngestion\Domain\Queries\GetRecentReadingsQuery;
use App\Contexts\MqttIngestion\Domain\Repositories\MqttReadingRepository;
use Illuminate\Support\Collection;

class GetRecentReadingsQueryService
{
    public function __construct(
        private readonly MqttReadingRepository $repository
    ) {}

    public function execute(GetRecentReadingsQuery $query): Collection
    {
        return $this->repository->getRecentReadings($query->limit);
    }
}
