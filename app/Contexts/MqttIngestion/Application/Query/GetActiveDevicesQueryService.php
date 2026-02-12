<?php

namespace App\Contexts\MqttIngestion\Application\Query;

use App\Contexts\MqttIngestion\Domain\Queries\GetActiveDevicesQuery;
use App\Contexts\MqttIngestion\Domain\Repositories\MqttReadingRepository;
use Illuminate\Support\Collection;

class GetActiveDevicesQueryService
{
    public function __construct(
        private readonly MqttReadingRepository $repository
    ) {}

    public function execute(GetActiveDevicesQuery $query): Collection
    {
        return $this->repository->getActiveDevices($query->hoursLimit ?? 24);
    }
}
