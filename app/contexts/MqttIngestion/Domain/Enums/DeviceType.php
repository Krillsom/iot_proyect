<?php

namespace App\Contexts\MqttIngestion\Domain\Enums;

enum DeviceType: string
{
    case GATEWAY = 'Gateway';
    case IBEACON = 'iBeacon';
    case SENSOR = 'Sensor';
    case UNKNOWN = 'Unknown';
}
