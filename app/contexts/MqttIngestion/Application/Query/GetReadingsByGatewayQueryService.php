<?php

namespace App\Contexts\MqttIngestion\Application\Query;

use App\Contexts\MqttIngestion\Domain\Queries\GetReadingsByGatewayQuery;
use App\Contexts\MqttIngestion\Domain\Repositories\MqttReadingRepository;
use Illuminate\Support\Collection;

class GetReadingsByGatewayQueryService
{
    public function __construct(
        private readonly MqttReadingRepository $repository
    ) {}

    public function execute(GetReadingsByGatewayQuery $query): Collection
    {
        return $this->repository->getByGateway($query->gatewayMac, $query->limit ?? 100);
    }
}
