<?php

namespace App\Contexts\Device\Application\Query;

use App\Contexts\Device\Application\Dto\DeviceDto;
use App\Contexts\Device\Domain\Queries\GetDevicesByStatusQuery;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;
use Illuminate\Support\Collection;

class GetDevicesByStatusQueryService
{
    public function __construct(
        private DeviceRepository $deviceRepository
    ) {}

    public function execute(GetDevicesByStatusQuery $query): Collection
    {
        $devices = $this->deviceRepository->getByStatus($query->status);

        return $devices->map(fn($device) => DeviceDto::fromModel($device));
    }
}
