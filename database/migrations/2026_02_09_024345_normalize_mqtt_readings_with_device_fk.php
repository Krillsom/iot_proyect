<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Normaliza mqtt_readings eliminando redundancia con devices:
     * - Migra datos de mqtt_readings → devices
     * - Agrega FK device_id y gateway_id
     * - Elimina device_mac, device_name, device_type, gateway_mac
     */
    public function up(): void
    {
        // PASO 1: Agregar columnas FK temporales (nullable)
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('id');
            $table->foreignId('gateway_id')->nullable()->after('device_id');
        });

        // PASO 2: Insertar dispositivos únicos desde mqtt_readings → devices
        $this->migrateDevicesToDevicesTable();

        // PASO 3: Actualizar mqtt_readings con device_id y gateway_id correctos
        $this->linkMqttReadingsToDevices();

        // PASO 4: Hacer NOT NULL y agregar índices/FK
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable(false)->change();
            $table->foreignId('gateway_id')->nullable(false)->change();
            
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('gateway_id')->references('id')->on('devices')->onDelete('cascade');
            
            $table->index(['device_id', 'data_timestamp']);
            $table->index(['gateway_id', 'data_timestamp']);
        });

        // PASO 5: Eliminar columnas redundantes
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->dropIndex(['gateway_mac']);
            $table->dropIndex(['device_mac', 'data_timestamp']);
            $table->dropIndex(['gateway_mac', 'device_type', 'data_timestamp']);
            
            $table->dropColumn([
                'gateway_mac',
                'device_mac',
                'device_type',
                'device_name',
            ]);
        });
    }

    /**
     * Migrar todos los dispositivos únicos desde mqtt_readings a devices
     */
    private function migrateDevicesToDevicesTable(): void
    {
        // Obtener todos los dispositivos únicos (device + gateway)
        $devices = DB::table('mqtt_readings')
            ->select('device_mac', 'device_type', 'device_name')
            ->distinct()
            ->get();

        foreach ($devices as $device) {
            // Mapear device_type de MQTT a devices.type
            $deviceType = match($device->device_type) {
                'Gateway' => 'gateway',
                'iBeacon' => 'sensor', // iBeacon es un tipo de sensor
                default => 'sensor'
            };

            // Insertar en devices si no existe
            DB::table('devices')->insertOrIgnore([
                'uuid' => (string) Str::uuid(),
                'name' => $device->device_name ?? $device->device_mac,
                'type' => $deviceType,
                'sensor_type' => $device->device_type === 'iBeacon' ? 'proximity' : null,
                'status' => 'online',
                'mac_address' => $device->device_mac,
                'parent_id' => null, // Se actualizará después con gateway parent
                'metadata' => json_encode([
                    'source' => 'mqtt_auto_registered',
                    'original_type' => $device->device_type,
                ]),
                'last_seen_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Actualizar parent_id: Los beacons tienen como parent su gateway
        $this->linkBeaconsToGateways();
    }

    /**
     * Vincular beacons a sus gateways (parent_id)
     */
    private function linkBeaconsToGateways(): void
    {
        // Obtener pares beacon-gateway desde mqtt_readings
        $beaconGatewayPairs = DB::table('mqtt_readings')
            ->select('device_mac', 'gateway_mac')
            ->where('device_type', 'iBeacon')
            ->distinct()
            ->get();

        foreach ($beaconGatewayPairs as $pair) {
            // Obtener UUID del gateway
            $gateway = DB::table('devices')
                ->where('mac_address', $pair->gateway_mac)
                ->first();

            if ($gateway) {
                // Actualizar parent_id del beacon
                DB::table('devices')
                    ->where('mac_address', $pair->device_mac)
                    ->update(['parent_id' => $gateway->uuid]);
            }
        }
    }

    /**
     * Actualizar mqtt_readings.device_id y gateway_id con IDs de devices
     */
    private function linkMqttReadingsToDevices(): void
    {
        // Actualizar device_id
        DB::statement("
            UPDATE mqtt_readings mr
            INNER JOIN devices d ON mr.device_mac = d.mac_address
            SET mr.device_id = d.id
        ");

        // Actualizar gateway_id
        DB::statement("
            UPDATE mqtt_readings mr
            INNER JOIN devices d ON mr.gateway_mac = d.mac_address
            SET mr.gateway_id = d.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar columnas originales
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->string('gateway_mac', 20)->after('id');
            $table->string('device_mac', 20)->after('topic');
            $table->string('device_type', 20)->after('device_mac');
            $table->string('device_name', 100)->nullable()->after('device_type');
        });

        // Poblar desde devices usando FK
        DB::statement("
            UPDATE mqtt_readings mr
            INNER JOIN devices gw ON mr.gateway_id = gw.id
            INNER JOIN devices dev ON mr.device_id = dev.id
            SET 
                mr.gateway_mac = gw.mac_address,
                mr.device_mac = dev.mac_address,
                mr.device_type = CASE 
                    WHEN dev.type = 'gateway' THEN 'Gateway'
                    WHEN dev.sensor_type = 'proximity' THEN 'iBeacon'
                    ELSE 'Unknown'
                END,
                mr.device_name = dev.name
        ");

        // Recrear índices
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->index('gateway_mac');
            $table->index(['device_mac', 'data_timestamp']);
            $table->index(['gateway_mac', 'device_type', 'data_timestamp']);
        });

        // Eliminar FK y columnas
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropForeign(['gateway_id']);
            $table->dropIndex(['device_id', 'data_timestamp']);
            $table->dropIndex(['gateway_id', 'data_timestamp']);
            $table->dropColumn(['device_id', 'gateway_id']);
        });
    }
};
