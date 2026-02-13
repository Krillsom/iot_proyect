<?php

namespace App\Contexts\Device\Infrastructure\Persistence;

use App\Contexts\Device\Domain\Device;
use App\Contexts\Device\Domain\Enums\DeviceStatus;
use App\Contexts\Device\Domain\Enums\DeviceType;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;
use Illuminate\Support\Collection;

class DeviceRepositoryEloquent implements DeviceRepository
{
    public function find(string $id): ?Device
    {
        return Device::find($id);
    }

    public function findByUuid(string $uuid): ?Device
    {
        return Device::where('uuid', $uuid)->first();
    }

    public function findByMacAddress(string $macAddress): ?Device
    {
        return Device::where('mac_address', $macAddress)->first();
    }

    public function getAll(): Collection
    {
        return Device::orderBy('created_at', 'desc')->get();
    }

    public function getByType(DeviceType $type): Collection
    {
        return Device::byType($type)->get();
    }

    public function getByStatus(DeviceStatus $status): Collection
    {
        return Device::where('status', $status)->get();
    }

    public function getOnline(): Collection
    {
        return Device::online()->get();
    }

    public function getOffline(): Collection
    {
        return Device::offline()->get();
    }

    // parent_id eliminado en migración 2026_02_10_001114
    public function getChildren(string $parentId): Collection
    {
        return collect(); // Jerarquía deshabilitada
    }

    // parent_id eliminado en migración 2026_02_10_001114
    public function getHierarchy(string $deviceId): array
    {
        return []; // Jerarquía deshabilitada
    }

    // parent_id eliminado en migración 2026_02_10_001114
    private function buildHierarchyTree(Device $device): Collection
    {
        return collect(); // Jerarquía deshabilitada
    }

    public function save(Device $device): Device
    {
        $device->save();
        return $device->fresh();
    }

    public function delete(string $id): bool
    {
        $device = $this->find($id);
        
        if (!$device) {
            return false;
        }

        return $device->delete();
    }

    public function exists(string $macAddress): bool
    {
        return Device::where('mac_address', $macAddress)->exists();
    }
}
