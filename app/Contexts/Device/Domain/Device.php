<?php

namespace App\Contexts\Device\Domain;

use App\Contexts\Device\Domain\Enums\DeviceStatus;
use App\Contexts\Device\Domain\Enums\DeviceType;
use App\Contexts\Device\Domain\Enums\SensorType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'name',
        'type',
        'sensor_type',
        'status',
        'mac_address',
        'ip_address',
        'metadata',
        'last_seen_at',
    ];

    protected $casts = [
        'type' => DeviceType::class,
        'sensor_type' => SensorType::class,
        'status' => DeviceStatus::class,
        'metadata' => 'array',
        'last_seen_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => DeviceStatus::OFFLINE,
        'metadata' => '{}',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    // Relaciones de jerarquía (parent_id removido en migración 2026_02_10_001114)
    // public function parent(): BelongsTo
    // {
    //     return $this->belongsTo(Device::class, 'parent_id');
    // }

    // public function children(): HasMany
    // {
    //     return $this->hasMany(Device::class, 'parent_id');
    // }

    // Scopes
    public function scopeOnline($query)
    {
        return $query->where('status', DeviceStatus::ONLINE);
    }

    public function scopeOffline($query)
    {
        return $query->where('status', DeviceStatus::OFFLINE);
    }

    public function scopeByType($query, DeviceType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSensors($query)
    {
        return $query->where('type', DeviceType::SENSOR);
    }

    public function scopeCameras($query)
    {
        return $query->where('type', DeviceType::CAMERA);
    }

    public function scopeGateways($query)
    {
        return $query->where('type', DeviceType::GATEWAY);
    }

    public function scopeEdges($query)
    {
        return $query->where('type', DeviceType::EDGE);
    }

    // Métodos de negocio
    public function isOnline(): bool
    {
        return $this->status === DeviceStatus::ONLINE;
    }

    public function isOffline(): bool
    {
        return $this->status === DeviceStatus::OFFLINE;
    }

    public function canHaveChildren(): bool
    {
        return $this->type->canHaveChildren();
    }

    public function isSensor(): bool
    {
        return $this->type === DeviceType::SENSOR;
    }

    public function isCamera(): bool
    {
        return $this->type === DeviceType::CAMERA;
    }

    public function isGateway(): bool
    {
        return $this->type === DeviceType::GATEWAY;
    }

    public function isEdge(): bool
    {
        return $this->type === DeviceType::EDGE;
    }

    public function updateLastSeen(): void
    {
        $this->update(['last_seen_at' => now()]);
    }

    public function markAsOnline(): void
    {
        $this->update(['status' => DeviceStatus::ONLINE]);
    }

    public function markAsOffline(): void
    {
        $this->update(['status' => DeviceStatus::OFFLINE]);
    }
}
