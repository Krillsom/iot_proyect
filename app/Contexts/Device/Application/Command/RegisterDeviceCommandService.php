<?php

namespace App\Contexts\Device\Application\Command;

use App\Contexts\Device\Application\Dto\DeviceDto;
use App\Contexts\Device\Domain\Commands\RegisterDeviceCommand;
use App\Contexts\Device\Domain\Device;
use App\Contexts\Device\Domain\Enums\DeviceStatus;
use App\Contexts\Device\Domain\Events\DeviceRegistered;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;
use App\Contexts\Device\Domain\ValueObjects\MacAddress;
use App\Contexts\Device\Domain\ValueObjects\IpAddress;
use Illuminate\Support\Str;

class RegisterDeviceCommandService
{
    public function __construct(
        private DeviceRepository $deviceRepository
    ) {}

    public function execute(RegisterDeviceCommand $command): DeviceDto
    {
        // Validar value objects
        $macAddress = new MacAddress($command->macAddress);
        $ipAddress = $command->ipAddress ? new IpAddress($command->ipAddress) : null;

        // Verificar que no exista el dispositivo
        if ($this->deviceRepository->exists($macAddress->value())) {
            throw new \DomainException('Device with this MAC address already exists');
        }

        // Validar sensor_type solo para sensores
        if ($command->type->value === 'sensor' && !$command->sensorType) {
            throw new \DomainException('Sensor type is required for sensor devices');
        }

        // Validar parent_id si existe
        if ($command->parentId) {
            $parent = $this->deviceRepository->findByUuid($command->parentId);
            if (!$parent) {
                throw new \DomainException('Parent device not found');
            }
            if (!$parent->canHaveChildren()) {
                throw new \DomainException('Parent device cannot have children');
            }
        }

        // Crear el dispositivo
        $device = new Device([
            'uuid' => Str::uuid()->toString(),
            'name' => $command->name,
            'type' => $command->type,
            'sensor_type' => $command->sensorType,
            'status' => DeviceStatus::OFFLINE,
            'mac_address' => $macAddress->normalized(),
            'ip_address' => $ipAddress?->value(),
            'parent_id' => $command->parentId,
            'metadata' => $command->metadata ?? [],
        ]);

        $device = $this->deviceRepository->save($device);

        // Disparar evento de dominio
        event(new DeviceRegistered($device));

        return DeviceDto::fromModel($device);
    }
}
