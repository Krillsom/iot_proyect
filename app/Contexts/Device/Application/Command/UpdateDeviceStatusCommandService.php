<?php

namespace App\Contexts\Device\Application\Command;

use App\Contexts\Device\Application\Dto\DeviceDto;
use App\Contexts\Device\Domain\Commands\UpdateDeviceStatusCommand;
use App\Contexts\Device\Domain\Events\DeviceStatusChanged;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;

class UpdateDeviceStatusCommandService
{
    public function __construct(
        private DeviceRepository $deviceRepository
    ) {}

    public function execute(UpdateDeviceStatusCommand $command): DeviceDto
    {
        $device = $this->deviceRepository->findByUuid($command->deviceId);

        if (!$device) {
            throw new \DomainException('Device not found');
        }

        $previousStatus = $device->status;

        $device->status = $command->status;
        $device->last_seen_at = now();
        $device = $this->deviceRepository->save($device);

        // Disparar evento si cambió el estado
        if ($previousStatus !== $command->status) {
            event(new DeviceStatusChanged($device, $previousStatus, $command->status));
        }

        return DeviceDto::fromModel($device);
    }
}
