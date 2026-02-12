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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->enum('type', ['sensor', 'camera', 'gateway', 'edge']);
            $table->enum('sensor_type', ['motion', 'gps', 'temperature', 'humidity', 'pressure', 'light', 'sound', 'proximity', 'other'])->nullable();
            $table->enum('status', ['online', 'offline', 'maintenance', 'error', 'inactive'])->default('offline');
            $table->string('mac_address')->unique();
            $table->string('ip_address')->nullable();
            $table->string('parent_id')->nullable(); // UUID del dispositivo padre
            $table->json('metadata')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            // Índices para optimizar queries
            $table->index('type');
            $table->index('status');
            $table->index('parent_id');
            $table->index('last_seen_at');
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
