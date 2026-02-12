<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Optimiza la tabla mqtt_readings eliminando campos sparse (con muchos NULL)
     * y consolidándolos en un campo JSON llamado specific_data
     */
    public function up(): void
    {
        Schema::table('mqtt_readings', function (Blueprint $table) {
            // Agregar campo JSON para datos específicos
            $table->json('specific_data')->nullable()->after('device_name');
        });

        // Migrar datos existentes al nuevo campo JSON (si hay datos)
        DB::statement("
            UPDATE mqtt_readings 
            SET specific_data = CASE 
                WHEN device_type = 'iBeacon' THEN JSON_OBJECT(
                    'ibeacon_uuid', ibeacon_uuid,
                    'ibeacon_major', ibeacon_major,
                    'ibeacon_minor', ibeacon_minor,
                    'rssi', rssi,
                    'ibeacon_tx_power', ibeacon_tx_power,
                    'battery', battery
                )
                WHEN device_type = 'Gateway' THEN JSON_OBJECT(
                    'gateway_free', gateway_free,
                    'gateway_load', gateway_load
                )
                ELSE NULL
            END
            WHERE specific_data IS NULL
        ");

        // Eliminar columnas individuales (ahora en JSON)
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->dropColumn([
                'ibeacon_uuid',
                'ibeacon_major',
                'ibeacon_minor',
                'rssi',
                'ibeacon_tx_power',
                'battery',
                'gateway_free',
                'gateway_load',
            ]);
        });

        // Crear índice virtual en JSON para queries comunes (RSSI)
        // Solo en MySQL 5.7+ / MariaDB 10.2+
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE mqtt_readings 
                ADD INDEX idx_rssi ((CAST(JSON_EXTRACT(specific_data, '$.rssi') AS SIGNED)))
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar índice virtual JSON
        if (DB::connection()->getDriverName() === 'mysql') {
            try {
                DB::statement("ALTER TABLE mqtt_readings DROP INDEX idx_rssi");
            } catch (\Exception $e) {
                // Ignorar si no existe
            }
        }

        // Restaurar columnas individuales
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->string('ibeacon_uuid', 50)->nullable();
            $table->integer('ibeacon_major')->nullable();
            $table->integer('ibeacon_minor')->nullable();
            $table->integer('rssi')->nullable();
            $table->integer('ibeacon_tx_power')->nullable();
            $table->integer('battery')->nullable();
            $table->integer('gateway_free')->nullable();
            $table->decimal('gateway_load', 8, 6)->nullable();
        });

        // Migrar datos de vuelta desde JSON
        DB::statement("
            UPDATE mqtt_readings 
            SET 
                ibeacon_uuid = JSON_UNQUOTE(JSON_EXTRACT(specific_data, '$.ibeacon_uuid')),
                ibeacon_major = JSON_EXTRACT(specific_data, '$.ibeacon_major'),
                ibeacon_minor = JSON_EXTRACT(specific_data, '$.ibeacon_minor'),
                rssi = JSON_EXTRACT(specific_data, '$.rssi'),
                ibeacon_tx_power = JSON_EXTRACT(specific_data, '$.ibeacon_tx_power'),
                battery = JSON_EXTRACT(specific_data, '$.battery'),
                gateway_free = JSON_EXTRACT(specific_data, '$.gateway_free'),
                gateway_load = JSON_EXTRACT(specific_data, '$.gateway_load')
            WHERE specific_data IS NOT NULL
        ");

        // Eliminar campo JSON
        Schema::table('mqtt_readings', function (Blueprint $table) {
            $table->dropColumn('specific_data');
        });
    }
};
