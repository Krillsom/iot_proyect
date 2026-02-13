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
        // ULTRA OPTIMIZADO: Primero obtener los IDs más recientes por dispositivo, luego fetch
        $recentIds = DB::table('mqtt_readings')
            ->select('device_id', DB::raw('MAX(id) as max_id'))
            ->where('data_timestamp', '>=', now()->subHours($hoursLimit))
            ->groupBy('device_id')
            ->limit(100);

        // Ahora obtener solo esos registros con JOINs
        return DB::table('mqtt_readings')
            ->joinSub($recentIds, 'recent', function ($join) {
                $join->on('mqtt_readings.id', '=', 'recent.max_id');
            })
            ->select(
                'mqtt_readings.*',
                'devices.mac_address as device_mac',
                'devices.name as device_name',
                'gw.mac_address as gateway_mac'
            )
            ->leftJoin('devices', 'mqtt_readings.device_id', '=', 'devices.id')
            ->leftJoin('devices as gw', 'mqtt_readings.gateway_id', '=', 'gw.id')
            ->orderBy('mqtt_readings.data_timestamp', 'desc')
            ->get()
            ->map(function ($reading) {
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
        // Usar whereBetween en lugar de whereDate para aprovechar índices
        $todayStart = today()->startOfDay();
        $todayEnd = today()->endOfDay();
        
        return [
            'total_readings' => MqttReading::count(),
            'total_devices' => DB::table('devices')->count(),
            'total_beacons' => DB::table('devices')->where('type', '!=', 'gateway')->count(),
            'total_gateways' => DB::table('devices')->where('type', 'gateway')->count(),
            'readings_today' => MqttReading::whereBetween('data_timestamp', [$todayStart, $todayEnd])->count(),
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
        // ULTRA OPTIMIZADO: Primero obtener gateway_ids de tipo gateway, luego contar
        $gatewayIds = DB::table('devices')
            ->where('type', 'gateway')
            ->pluck('id', 'mac_address');
        
        if ($gatewayIds->isEmpty()) {
            return collect();
        }
        
        // Contar devices por gateway usando solo índices
        $counts = DB::table('mqtt_readings')
            ->select('gateway_id', DB::raw('COUNT(DISTINCT device_id) as device_count'))
            ->whereIn('gateway_id', $gatewayIds->values())
            ->where('data_timestamp', '>=', now()->subHours(24))
            ->groupBy('gateway_id')
            ->get()
            ->keyBy('gateway_id');
        
        // Mapear IDs a MACs y retornar como objetos
        return $gatewayIds->map(function ($gatewayId, $mac) use ($counts) {
            return (object) [
                'gateway_mac' => $mac,
                'device_count' => $counts->get($gatewayId)?->device_count ?? 0,
            ];
        })->values();
    }

    public function getTriangulationData(int $hoursLimit = 24): Collection
    {
        // ULTRA OPTIMIZADO: Obtener últimos IDs por device+topic, luego fetch los datos
        $cutoffTime = now()->subHours($hoursLimit);

        // Subconsulta para G1 - solo último ID por device
        $g1Latest = DB::table('mqtt_readings as mr1')
            ->select('device_id', DB::raw('MAX(id) as max_id'))
            ->where('topic', '/sur/g1/status')
            ->where('data_timestamp', '>=', $cutoffTime)
            ->groupBy('device_id');

        // Subconsulta para G2 - solo último ID por device
        $g2Latest = DB::table('mqtt_readings as mr2')
            ->select('device_id', DB::raw('MAX(id) as max_id'))
            ->where('topic', '/sur/g2/status')
            ->where('data_timestamp', '>=', $cutoffTime)
            ->groupBy('device_id');

        // Obtener datos de G1
        $g1Data = DB::table('mqtt_readings')
            ->joinSub($g1Latest, 'g1l', function ($join) {
                $join->on('mqtt_readings.id', '=', 'g1l.max_id');
            })
            ->select(
                'mqtt_readings.device_id',
                DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(mqtt_readings.specific_data, "$.rssi")) AS SIGNED) as g1_rssi'),
                'mqtt_readings.data_timestamp as g1_last_seen'
            )
            ->get()
            ->keyBy('device_id');

        // Obtener datos de G2
        $g2Data = DB::table('mqtt_readings')
            ->joinSub($g2Latest, 'g2l', function ($join) {
                $join->on('mqtt_readings.id', '=', 'g2l.max_id');
            })
            ->select(
                'mqtt_readings.device_id',
                DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(mqtt_readings.specific_data, "$.rssi")) AS SIGNED) as g2_rssi'),
                'mqtt_readings.data_timestamp as g2_last_seen'
            )
            ->get()
            ->keyBy('device_id');

        // Combinar con devices
        return DB::table('devices')
            ->select('id', 'mac_address', 'name', 'type', 'last_seen_at')
            ->where('sensor_type', 'proximity')
            ->get()
            ->map(function ($device) use ($g1Data, $g2Data) {
                $g1 = $g1Data->get($device->id);
                $g2 = $g2Data->get($device->id);
                
                // Solo incluir si al menos hay lectura de 1 gateway
                if (!$g1 && !$g2) {
                    return null;
                }
                
                $device->last_seen_at = $device->last_seen_at ? \Carbon\Carbon::parse($device->last_seen_at) : null;
                $device->g1_rssi = $g1->g1_rssi ?? null;
                $device->g1_last_seen = $g1 ? \Carbon\Carbon::parse($g1->g1_last_seen) : null;
                $device->g2_rssi = $g2->g2_rssi ?? null;
                $device->g2_last_seen = $g2 ? \Carbon\Carbon::parse($g2->g2_last_seen) : null;
                
                return $device;
            })
            ->filter() // Remover nulls
            ->values();
    }
}

