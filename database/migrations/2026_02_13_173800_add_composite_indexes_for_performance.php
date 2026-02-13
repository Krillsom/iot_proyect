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
        $connection = Schema::getConnection();
        $dbName = $connection->getDatabaseName();
        
        // Verificar índices existentes en mqtt_readings
        $existingIndexes = $connection->select("
            SELECT DISTINCT INDEX_NAME 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'mqtt_readings'
        ", [$dbName]);
        
        $existingIndexNames = array_column($existingIndexes, 'INDEX_NAME');
        
        Schema::table('mqtt_readings', function (Blueprint $table) use ($existingIndexNames) {
            // Índice compuesto para DevicesByGateway (gateway_id + data_timestamp + device_id)
            // Esto hace COUNT(DISTINCT device_id) mucho más rápido
            if (!in_array('idx_mqtt_gateway_timestamp_device', $existingIndexNames)) {
                $table->index(['gateway_id', 'data_timestamp', 'device_id'], 'idx_mqtt_gateway_timestamp_device');
            }
            
            // Índice compuesto para triangulation queries (topic + data_timestamp + device_id)
            if (!in_array('idx_mqtt_topic_timestamp_device', $existingIndexNames)) {
                $table->index(['topic', 'data_timestamp', 'device_id'], 'idx_mqtt_topic_timestamp_device');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->dropIndex('idx_mqtt_gateway_timestamp_device');
            $table->dropIndex('idx_mqtt_topic_timestamp_device');
        });
    }
};
