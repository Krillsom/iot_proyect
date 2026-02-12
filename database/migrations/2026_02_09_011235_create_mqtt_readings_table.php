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
        Schema::create('mqtt_readings', function (Blueprint $table) {
            $table->id();
            
            // Identificación del gateway que reporta
            $table->string('gateway_mac', 20)->index();
            $table->string('topic', 100)->index();
            
            // Información del dispositivo
            $table->string('device_mac', 20)->index();
            $table->string('device_type', 20)->index(); // Gateway, iBeacon
            $table->string('device_name', 100)->nullable();
            
            // Datos específicos de iBeacon
            $table->string('ibeacon_uuid', 50)->nullable();
            $table->integer('ibeacon_major')->nullable();
            $table->integer('ibeacon_minor')->nullable();
            $table->integer('rssi')->nullable(); // Potencia de señal
            $table->integer('ibeacon_tx_power')->nullable();
            $table->integer('battery')->nullable();
            
            // Datos específicos de Gateway
            $table->integer('gateway_free')->nullable();
            $table->decimal('gateway_load', 8, 6)->nullable();
            
            // Almacenamiento completo del JSON
            $table->json('raw_data');
            
            // Timestamp del dato original
            $table->timestamp('data_timestamp')->index();
            
            $table->timestamps();
            
            // Índices compuestos para queries rápidas
            $table->index(['gateway_mac', 'device_type', 'data_timestamp']);
            $table->index(['device_mac', 'data_timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mqtt_readings');
    }
};
