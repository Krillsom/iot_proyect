<?php

namespace App\Contexts\MqttIngestion\Infrastructure\Persistence;

use App\Contexts\MqttIngestion\Domain\MqttReading;
use App\Contexts\MqttIngestion\Domain\Repositories\MqttReadingRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MqttReadingRepositoryEloquent implements MqttReadingRepository
{
    public function save(array $data): MqttReading
    {
        return MqttReading::create($data);
    }

    public function saveBulk(array $readings): int
    {
        // Inserción masiva optimizada
        return DB::table('mqtt_readings')->insert($readings);
    }

    public function getActiveDevices(int $hoursLimit = 24): Collection
    {
        // Optimizado: usar joins en lugar de with() para evitar memory exhaustion
        return DB::table('mqtt_readings')
            ->select(
                'mqtt_readings.*',
                'devices.mac_address as device_mac',
                'devices.name as device_name',
                'gw.mac_address as gateway_mac',
                DB::raw('MAX(mqtt_readings.data_timestamp) as last_seen')
            )
            ->leftJoin('devices', 'mqtt_readings.device_id', '=', 'devices.id')
            ->leftJoin('devices as gw', 'mqtt_readings.gateway_id', '=', 'gw.id')
            ->where('mqtt_readings.data_timestamp', '>=', now()->subHours($hoursLimit))
            ->groupBy('mqtt_readings.device_id', 'mqtt_readings.gateway_id', 'mqtt_readings.id', 'mqtt_readings.topic', 'mqtt_readings.specific_data', 'mqtt_readings.raw_data', 'mqtt_readings.data_timestamp', 'mqtt_readings.created_at', 'mqtt_readings.updated_at', 'devices.mac_address', 'devices.name', 'gw.mac_address')
            ->orderBy('last_seen', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($reading) {
                $reading->last_seen = \Carbon\Carbon::parse($reading->last_seen);
                $reading->data_timestamp = \Carbon\Carbon::parse($reading->data_timestamp);
                $reading->specific_data = json_decode($reading->specific_data, true);
                $reading->raw_data = json_decode($reading->raw_data, true);
                return $reading;
            });
    }

    public function getByGateway(string $gatewayMac, int $limit = 100): Collection
    {
        // Buscar gateway_id por MAC
        $gatewayId = DB::table('devices')->where('mac_address', $gatewayMac)->value('id');
        
        if (!$gatewayId) {
            return collect();
        }

        // Optimizado: usar joins en lugar de with()
        return DB::table('mqtt_readings')
            ->select('mqtt_readings.*', 'devices.mac_address as device_mac', 'devices.name as device_name')
            ->leftJoin('devices', 'mqtt_readings.device_id', '=', 'devices.id')
            ->where('mqtt_readings.gateway_id', $gatewayId)
            ->orderBy('mqtt_readings.data_timestamp', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($reading) {
                $reading->data_timestamp = \Carbon\Carbon::parse($reading->data_timestamp);
                $reading->specific_data = json_decode($reading->specific_data, true);
                $reading->raw_data = json_decode($reading->raw_data, true);
                return $reading;
            });
    }

    public function getRecentReadings(int $limit = 20): Collection
    {
        // Optimizado: usar joins en lugar de with()
        return DB::table('mqtt_readings')
            ->select('mqtt_readings.*', 'devices.mac_address as device_mac', 'devices.name as device_name', 'gw.mac_address as gateway_mac')
            ->leftJoin('devices', 'mqtt_readings.device_id', '=', 'devices.id')
            ->leftJoin('devices as gw', 'mqtt_readings.gateway_id', '=', 'gw.id')
            ->orderBy('mqtt_readings.data_timestamp', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($reading) {
                $reading->data_timestamp = \Carbon\Carbon::parse($reading->data_timestamp);
                $reading->specific_data = json_decode($reading->specific_data, true);
                $reading->raw_data = json_decode($reading->raw_data, true);
                return $reading;
            });
    }

    public function getDashboardStats(): array
    {
        return [
            'total_readings' => MqttReading::count(),
            'total_devices' => DB::table('devices')->count(),
            'total_beacons' => DB::table('devices')->where('type', '!=', 'gateway')->count(),
            'total_gateways' => DB::table('devices')->where('type', 'gateway')->count(),
            'readings_today' => MqttReading::whereDate('data_timestamp', today())->count(),
            'readings_last_hour' => MqttReading::where('data_timestamp', '>=', now()->subHour())->count(),
        ];
    }

    public function countActiveDevices(): int
    {
        return DB::table('mqtt_readings')
            ->where('data_timestamp', '>=', now()->subHours(24))
            ->distinct('device_id')
            ->count('device_id');
    }

    public function countActiveBeacons(): int
    {
        return DB::table('mqtt_readings')
            ->join('devices', 'mqtt_readings.device_id', '=', 'devices.id')
            ->where('devices.sensor_type', 'proximity') // iBeacons son proximity sensors
            ->where('mqtt_readings.data_timestamp', '>=', now()->subHours(24))
            ->distinct('mqtt_readings.device_id')
            ->count('mqtt_readings.device_id');
    }

    public function countActiveGateways(): int
    {
        return DB::table('mqtt_readings')
            ->join('devices', 'mqtt_readings.device_id', '=', 'devices.id')
            ->where('devices.type', 'gateway')
            ->where('mqtt_readings.data_timestamp', '>=', now()->subHours(24))
            ->distinct('mqtt_readings.device_id')
            ->count('mqtt_readings.device_id');
    }

    public function getDevicesByGateway(): Collection
    {
        return DB::table('mqtt_readings')
            ->join('devices as gateways', 'mqtt_readings.gateway_id', '=', 'gateways.id')
            ->join('devices as beacons', 'mqtt_readings.device_id', '=', 'beacons.id')
            ->select('gateways.mac_address as gateway_mac', DB::raw('COUNT(DISTINCT mqtt_readings.device_id) as device_count'))
            ->where('beacons.sensor_type', 'proximity')
            ->groupBy('gateways.mac_address')
            ->get();
    }

    public function getTriangulationData(int $hoursLimit = 24): Collection
    {
        // Obtener lecturas de G1 y G2 con RSSI
        $cutoffTime = now()->subHours($hoursLimit);

        // Subconsulta para G1 - obtener el último registro por device_id
        $g1Readings = DB::table('mqtt_readings as mr1')
            ->select(
                'mr1.device_id',
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(JSON_UNQUOTE(JSON_EXTRACT(mr1.specific_data, "$.rssi")) ORDER BY mr1.data_timestamp DESC), ",", 1) as g1_rssi'),
                DB::raw('MAX(mr1.data_timestamp) as g1_last_seen')
            )
            ->where('mr1.topic', '/sur/g1/status')
            ->where('mr1.data_timestamp', '>=', $cutoffTime)
            ->groupBy('mr1.device_id');

        // Subconsulta para G2 - obtener el último registro por device_id
        $g2Readings = DB::table('mqtt_readings as mr2')
            ->select(
                'mr2.device_id',
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(JSON_UNQUOTE(JSON_EXTRACT(mr2.specific_data, "$.rssi")) ORDER BY mr2.data_timestamp DESC), ",", 1) as g2_rssi'),
                DB::raw('MAX(mr2.data_timestamp) as g2_last_seen')
            )
            ->where('mr2.topic', '/sur/g2/status')
            ->where('mr2.data_timestamp', '>=', $cutoffTime)
            ->groupBy('mr2.device_id');

        // Join con devices y combinar G1 + G2
        return DB::table('devices as d')
            ->leftJoinSub($g1Readings, 'g1', function ($join) {
                $join->on('d.id', '=', 'g1.device_id');
            })
            ->leftJoinSub($g2Readings, 'g2', function ($join) {
                $join->on('d.id', '=', 'g2.device_id');
            })
            ->select(
                'd.id',
                'd.mac_address',
                'd.name',
                'd.type',
                'd.last_seen_at',
                'g1.g1_rssi',
                'g1.g1_last_seen',
                'g2.g2_rssi',
                'g2.g2_last_seen'
            )
            ->where('d.sensor_type', 'proximity') // Solo iBeacons
            ->whereNotNull(DB::raw('COALESCE(g1.g1_rssi, g2.g2_rssi)')) // Al menos detectado por 1 gateway
            ->orderBy('d.mac_address')
            ->get()
            ->map(function ($device) {
                // Convertir timestamps a Carbon
                $device->last_seen_at = $device->last_seen_at ? \Carbon\Carbon::parse($device->last_seen_at) : null;
                $device->g1_last_seen = $device->g1_last_seen ? \Carbon\Carbon::parse($device->g1_last_seen) : null;
                $device->g2_last_seen = $device->g2_last_seen ? \Carbon\Carbon::parse($device->g2_last_seen) : null;
                
                // Convertir RSSI a int (viene como string del JSON)
                $device->g1_rssi = $device->g1_rssi !== null ? (int) $device->g1_rssi : null;
                $device->g2_rssi = $device->g2_rssi !== null ? (int) $device->g2_rssi : null;
                
                return $device;
            });
    }
}

