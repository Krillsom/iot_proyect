<?php

namespace App\Contexts\MqttIngestion\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Evento emitido cuando se detecta un dispositivo en MQTT
 * 
 * Permite desacoplar MqttIngestion de Device Context
 */
class DeviceDetected
{
    use Dispatchable;

    public function __construct(
        public readonly string $macAddress,
        public readonly string $mqttType,  // 'Gateway' | 'iBeacon'
        public readonly string $name,
        public readonly ?string $gatewayMac = null
    ) {}
}
