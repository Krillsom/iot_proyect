<?php

namespace App\Contexts\Device\Application\Query;

use App\Contexts\Device\Domain\Repositories\DeviceRepository;
use Illuminate\Support\Collection;

class GetAllDevicesQueryService
{
    public function __construct(
        private DeviceRepository $deviceRepository
    ) {}

    public function execute(): Collection
    {
        return $this->deviceRepository->getAll();
    }
}
