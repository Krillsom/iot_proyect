<?php

namespace App\Contexts\Device\Application\Listeners;

use App\Contexts\MqttIngestion\Domain\Events\DeviceDetected;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;
use App\Contexts\Device\Domain\Device;
use App\Contexts\Device\Domain\Enums\DeviceType;
use App\Contexts\Device\Domain\Enums\DeviceStatus;
use App\Contexts\Device\Domain\Enums\SensorType;
use Illuminate\Support\Str;

/**
 * Listener que auto-registra dispositivos detectados por MQTT
 * 
 * Ejecuta SINCRÓNICAMENTE para garantizar que devices existan
 * antes de insertar mqtt_readings (foreign key constraint)
 */
class AutoRegisterDeviceListener
{
    public function __construct(
        private readonly DeviceRepository $repository
    ) {}

    /**
     * Handle the event.
     */
    public function handle(DeviceDetected $event): void
    {
        // Verificar si ya existe
        $existingDevice = $this->repository->findByMacAddress($event->macAddress);
        
        if ($existingDevice) {
            // Actualizar last_seen_at si queremos
            $existingDevice->last_seen_at = now();
            $existingDevice->status = DeviceStatus::ONLINE;
            $this->repository->save($existingDevice);
            return;
        }
        
        // Mapear tipo MQTT a tipo Device
        $deviceType = match($event->mqttType) {
            'Gateway' => DeviceType::GATEWAY,
            'iBeacon' => DeviceType::SENSOR,
            default => DeviceType::SENSOR
        };
        
        $sensorType = ($event->mqttType === 'iBeacon') ? SensorType::PROXIMITY : null;
        
        // Crear nuevo dispositivo
        $device = new Device([
            'uuid' => (string) Str::uuid(),
            'name' => $event->name,
            'type' => $deviceType,
            'sensor_type' => $sensorType,
            'status' => DeviceStatus::ONLINE,
            'mac_address' => $event->macAddress,
            'metadata' => [
                'source' => 'mqtt_auto_registered',
                'original_mqtt_type' => $event->mqttType,
                'registered_at' => now()->toIso8601String(),
            ],
            'last_seen_at' => now(),
        ]);
        
        $this->repository->save($device);
    }
}
