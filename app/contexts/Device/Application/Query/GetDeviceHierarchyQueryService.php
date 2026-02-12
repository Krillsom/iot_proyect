<?php

namespace App\Contexts\Device\Application\Query;

use App\Contexts\Device\Domain\Queries\GetDeviceHierarchyQuery;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;

class GetDeviceHierarchyQueryService
{
    public function __construct(
        private DeviceRepository $deviceRepository
    ) {}

    public function execute(GetDeviceHierarchyQuery $query): array
    {
        return $this->deviceRepository->getHierarchy($query->deviceId);
    }
}
