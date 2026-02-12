<?php

namespace App\Contexts\MqttIngestion\Domain;

use App\Contexts\Device\Domain\Device;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MqttReading extends Model
{
    protected $fillable = [
        'device_id',      // ✅ FK normalizada
        'gateway_id',     // ✅ FK normalizada
        'topic',
        'specific_data',  // ✅ JSON con datos específicos por tipo
        'raw_data',
        'data_timestamp',
    ];

    protected $casts = [
        'specific_data' => 'array',  // ✅ Auto-cast JSON a array
        'raw_data' => 'array',
        'data_timestamp' => 'datetime',
    ];

    /**
     * Relación: Dispositivo que generó esta lectura
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    /**
     * Relación: Gateway que reportó esta lectura
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'gateway_id');
    }

    /**
     * Scope para obtener solo iBeacons
     */
    public function scopeBeacons($query)
    {
        return $query->whereHas('device', function ($q) {
            $q->where('sensor_type', 'proximity'); // iBeacons son proximity sensors
        });
    }

    /**
     * Scope para obtener solo Gateways
     */
    public function scopeGateways($query)
    {
        return $query->whereHas('device', function ($q) {
            $q->where('type', 'gateway');
        });
    }

    /**
     * Scope para obtener lecturas de las últimas N horas
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('data_timestamp', '>=', now()->subHours($hours));
    }
}
