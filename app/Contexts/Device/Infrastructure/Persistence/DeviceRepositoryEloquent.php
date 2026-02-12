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
        return Device::with('parent')->orderBy('created_at', 'desc')->get();
    }

    public function getByType(DeviceType $type): Collection
    {
        return Device::byType($type)->with('parent')->get();
    }

    public function getByStatus(DeviceStatus $status): Collection
    {
        return Device::where('status', $status)->with('parent')->get();
    }

    public function getOnline(): Collection
    {
        return Device::online()->with('parent')->get();
    }

    public function getOffline(): Collection
    {
        return Device::offline()->with('parent')->get();
    }

    public function getChildren(string $parentId): Collection
    {
        $parent = $this->findByUuid($parentId);
        
        if (!$parent) {
            return collect();
        }

        return Device::where('parent_id', $parent->uuid)->get();
    }

    public function getHierarchy(string $deviceId): array
    {
        $device = $this->findByUuid($deviceId);

        if (!$device) {
            return [];
        }

        return [
            'device' => $device,
            'children' => $this->buildHierarchyTree($device),
        ];
    }

    private function buildHierarchyTree(Device $device): Collection
    {
        $children = Device::where('parent_id', $device->uuid)->get();

        return $children->map(function ($child) {
            return [
                'device' => $child,
                'children' => $this->buildHierarchyTree($child),
            ];
        });
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
