<?php

namespace App\Contexts\Device\Domain\Repositories;

use App\Contexts\Device\Domain\Device;
use App\Contexts\Device\Domain\Enums\DeviceStatus;
use App\Contexts\Device\Domain\Enums\DeviceType;
use Illuminate\Support\Collection;

interface DeviceRepository
{
    public function find(string $id): ?Device;
    
    public function findByUuid(string $uuid): ?Device;
    
    public function findByMacAddress(string $macAddress): ?Device;
    
    public function getAll(): Collection;
    
    public function getByType(DeviceType $type): Collection;
    
    public function getByStatus(DeviceStatus $status): Collection;
    
    public function getOnline(): Collection;
    
    public function getOffline(): Collection;
    
    public function getChildren(string $parentId): Collection;
    
    public function getHierarchy(string $deviceId): array;
    
    public function save(Device $device): Device;
    
    public function delete(string $id): bool;
    
    public function exists(string $macAddress): bool;
}
