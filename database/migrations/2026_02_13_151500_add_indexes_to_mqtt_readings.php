<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usar DB raw para verificar si existen antes de crear
        $connection = Schema::getConnection();
        $dbName = $connection->getDatabaseName();
        
        // Verificar y crear índices en mqtt_readings
        $existingIndexes = $connection->select("
            SELECT DISTINCT INDEX_NAME 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'mqtt_readings'
        ", [$dbName]);
        
        $existingIndexNames = array_column($existingIndexes, 'INDEX_NAME');
        
        Schema::table('mqtt_readings', function (Blueprint $table) use ($existingIndexNames) {
            if (!in_array('idx_mqtt_topic', $existingIndexNames)) {
                $table->index('topic', 'idx_mqtt_topic');
            }
            if (!in_array('idx_mqtt_timestamp', $existingIndexNames)) {
                $table->index('data_timestamp', 'idx_mqtt_timestamp');
            }
            if (!in_array('idx_mqtt_device_timestamp', $existingIndexNames)) {
                $table->index(['device_id', 'data_timestamp'], 'idx_mqtt_device_timestamp');
            }
            if (!in_array('idx_mqtt_gateway_timestamp', $existingIndexNames)) {
                $table->index(['gateway_id', 'data_timestamp'], 'idx_mqtt_gateway_timestamp');
            }
            if (!in_array('idx_mqtt_topic_timestamp', $existingIndexNames)) {
                $table->index(['topic', 'data_timestamp'], 'idx_mqtt_topic_timestamp');
            }
        });

        // Verificar y crear índices en devices
        $existingDeviceIndexes = $connection->select("
            SELECT DISTINCT INDEX_NAME 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'devices'
        ", [$dbName]);
        
        $existingDeviceIndexNames = array_column($existingDeviceIndexes, 'INDEX_NAME');
        
        Schema::table('devices', function (Blueprint $table) use ($existingDeviceIndexNames) {
            if (!in_array('idx_devices_sensor_type', $existingDeviceIndexNames)) {
                $table->index('sensor_type', 'idx_devices_sensor_type');
            }
            if (!in_array('idx_devices_type', $existingDeviceIndexNames)) {
                $table->index('type', 'idx_devices_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->dropIndex('idx_mqtt_topic');
            $table->dropIndex('idx_mqtt_timestamp');
            $table->dropIndex('idx_mqtt_device_timestamp');
            $table->dropIndex('idx_mqtt_gateway_timestamp');
            $table->dropIndex('idx_mqtt_topic_timestamp');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex('idx_devices_sensor_type');
            $table->dropIndex('idx_devices_type');
        });
    }
};
