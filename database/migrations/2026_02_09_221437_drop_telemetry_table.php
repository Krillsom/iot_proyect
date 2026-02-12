<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Elimina la tabla telemetry ya que solo usaremos mqtt_readings
     */
    public function up(): void
    {
        Schema::dropIfExists('telemetry');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear tabla telemetry básica (solo para rollback)
        Schema::create('telemetry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->enum('type', ['motion', 'gps', 'temperature', 'humidity', 'pressure', 'light', 'sound', 'proximity', 'generic']);
            $table->json('value');
            $table->json('metadata')->nullable();
            $table->timestamp('recorded_at')->index();
            $table->timestamp('created_at')->nullable();

            $table->index(['device_id', 'recorded_at']);
            $table->index(['device_id', 'type', 'recorded_at']);
            $table->index(['type', 'recorded_at']);
            $table->index('recorded_at');
        });
    }
};
