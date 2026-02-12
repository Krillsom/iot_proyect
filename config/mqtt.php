<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MQTT Broker Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the MQTT broker connection used for IoT data ingestion.
    |
    */

    'host' => env('MQTT_HOST', 'localhost'),

    'port' => env('MQTT_PORT', 1883),

    'username' => env('MQTT_USERNAME'),

    'password' => env('MQTT_PASSWORD'),

    'topic' => env('MQTT_TOPIC', '#'),

    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    */

    'keep_alive_interval' => 60,

    'last_will_topic' => 'iot/disconnect',

    'last_will_message' => 'Laravel MQTT client disconnected',

    'last_will_qos' => 0,

];
