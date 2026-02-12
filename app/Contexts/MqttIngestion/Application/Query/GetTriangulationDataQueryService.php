<?php

namespace App\Contexts\MqttIngestion\Application\Query;

use App\Contexts\MqttIngestion\Domain\Queries\GetTriangulationDataQuery;
use App\Contexts\MqttIngestion\Domain\Repositories\MqttReadingRepository;
use Illuminate\Support\Collection;

class GetTriangulationDataQueryService
{
    public function __construct(
        private readonly MqttReadingRepository $mqttReadingRepository
    ) {}

    public function execute(GetTriangulationDataQuery $query): Collection
    {
        return $this->mqttReadingRepository->getTriangulationData($query->hoursLimit);
    }
}
