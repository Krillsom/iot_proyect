<?php

namespace App\Contexts\Device\Application\Command;

use App\Contexts\Device\Application\Dto\DeviceDto;
use App\Contexts\Device\Domain\Commands\UpdateDeviceConfigCommand;
use App\Contexts\Device\Domain\Events\DeviceConfigUpdated;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;
use App\Contexts\Device\Domain\ValueObjects\IpAddress;

class UpdateDeviceConfigCommandService
{
    public function __construct(
        private DeviceRepository $deviceRepository
    ) {}

    public function execute(UpdateDeviceConfigCommand $command): DeviceDto
    {
        $device = $this->deviceRepository->findByUuid($command->deviceId);

        if (!$device) {
            throw new \DomainException('Device not found');
        }

        if ($command->name) {
            $device->name = $command->name;
        }

        if ($command->ipAddress) {
            $ipAddress = new IpAddress($command->ipAddress);
            $device->ip_address = $ipAddress->value();
        }

        if ($command->metadata) {
            $device->metadata = array_merge($device->metadata ?? [], $command->metadata);
        }

        $device = $this->deviceRepository->save($device);

        event(new DeviceConfigUpdated($device));

        return DeviceDto::fromModel($device);
    }
}
