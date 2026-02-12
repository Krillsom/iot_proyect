<?php

namespace App\Contexts\Device\Application\Query;

use App\Contexts\Device\Application\Dto\DeviceDto;
use App\Contexts\Device\Domain\Queries\GetDeviceQuery;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;

class GetDeviceQueryService
{
    public function __construct(
        private DeviceRepository $deviceRepository
    ) {}

    public function execute(GetDeviceQuery $query): ?DeviceDto
    {
        $device = $this->deviceRepository->findByUuid($query->deviceId);

        return $device ? DeviceDto::fromModel($device) : null;
    }
}
