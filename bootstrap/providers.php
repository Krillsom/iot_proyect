<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Contexts\Device\Providers\DeviceServiceProvider::class,
    App\Contexts\MqttIngestion\Providers\MqttIngestionServiceProvider::class,
];
