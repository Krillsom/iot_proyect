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
        Schema::table('mqtt_readings', function (Blueprint $table) {
            // Índice para búsquedas por topic
            $table->index('topic', 'idx_mqtt_topic');
            
            // Índice para búsquedas por timestamp
            $table->index('data_timestamp', 'idx_mqtt_timestamp');
            
            // Índice compuesto para búsquedas por dispositivo y fecha
            $table->index(['device_id', 'data_timestamp'], 'idx_mqtt_device_timestamp');
            
            // Índice compuesto para búsquedas por gateway y fecha
            $table->index(['gateway_id', 'data_timestamp'], 'idx_mqtt_gateway_timestamp');
            
            // Índice compuesto para topic + timestamp (optimiza queries de estado de gateways)
            $table->index(['topic', 'data_timestamp'], 'idx_mqtt_topic_timestamp');
        });

        Schema::table('devices', function (Blueprint $table) {
            // Índice para búsquedas por sensor_type
            $table->index('sensor_type', 'idx_devices_sensor_type');
            
            // Índice para búsquedas por type
            $table->index('type', 'idx_devices_type');
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
