<?php

namespace App\Contexts\Device\Application\Query;

use App\Contexts\Device\Application\Dto\DeviceDto;
use App\Contexts\Device\Domain\Queries\GetDevicesByTypeQuery;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;
use Illuminate\Support\Collection;

class GetDevicesByTypeQueryService
{
    public function __construct(
        private DeviceRepository $deviceRepository
    ) {}

    public function execute(GetDevicesByTypeQuery $query): Collection
    {
        $devices = $this->deviceRepository->getByType($query->type);

        return $devices->map(fn($device) => DeviceDto::fromModel($device));
    }
}
