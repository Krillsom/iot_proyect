<?php

namespace App\Contexts\Device\Application\Command;

use App\Contexts\Device\Domain\Commands\DeleteDeviceCommand;
use App\Contexts\Device\Domain\Events\DeviceDeleted;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;

class DeleteDeviceCommandService
{
    public function __construct(
        private DeviceRepository $deviceRepository
    ) {}

    public function execute(DeleteDeviceCommand $command): bool
    {
        $device = $this->deviceRepository->findByUuid($command->deviceId);

        if (!$device) {
            throw new \DomainException('Device not found');
        }

        // Verificar si tiene hijos
        $children = $this->deviceRepository->getChildren($device->uuid);
        if ($children->isNotEmpty()) {
            throw new \DomainException('Cannot delete device with children. Delete children first.');
        }

        $deleted = $this->deviceRepository->delete($device->id);

        if ($deleted) {
            event(new DeviceDeleted($command->deviceId));
        }

        return $deleted;
    }
}
