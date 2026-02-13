<?php

namespace App\Contexts\MqttIngestion\Http\Controllers;

use App\Contexts\MqttIngestion\Application\Query\GetActiveDevicesQueryService;
use App\Contexts\MqttIngestion\Application\Query\GetDashboardStatsQueryService;
use App\Contexts\MqttIngestion\Application\Query\GetRecentReadingsQueryService;
use App\Contexts\MqttIngestion\Application\Query\GetTriangulationDataQueryService;
use App\Contexts\MqttIngestion\Domain\Queries\GetActiveDevicesQuery;
use App\Contexts\MqttIngestion\Domain\Queries\GetDashboardStatsQuery;
use App\Contexts\MqttIngestion\Domain\Queries\GetRecentReadingsQuery;
use App\Contexts\MqttIngestion\Domain\Queries\GetTriangulationDataQuery;
use App\Contexts\MqttIngestion\Domain\Repositories\MqttReadingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MqttDashboardController
{
    public function __construct(
        private readonly GetDashboardStatsQueryService $statsQueryService,
        private readonly GetActiveDevicesQueryService $activeDevicesQueryService,
        private readonly GetRecentReadingsQueryService $recentReadingsQueryService,
        private readonly GetTriangulationDataQueryService $triangulationQueryService,
        private readonly MqttReadingRepository $repository
    ) {}

    /**
     * Display the main MQTT dashboard
     */
    public function index()
    {
        // DEBUG: Log todas las queries con sus tiempos
        DB::listen(function ($query) {
            \Log::info('Query: ' . $query->sql . ' [' . $query->time . 'ms]');
        });
        
        $startTime = microtime(true);
        
        // Cache de stats y datos pesados por 5 segundos
        $t1 = microtime(true);
        $stats = Cache::remember('dashboard.stats', 5, function () {
            return $this->statsQueryService->execute(new GetDashboardStatsQuery());
        });
        \Log::info('Stats: ' . round((microtime(true) - $t1) * 1000, 2) . 'ms');
        
        $t2 = microtime(true);
        $activeDevices = Cache::remember('dashboard.active_devices', 5, function () {
            return $this->activeDevicesQueryService->execute(new GetActiveDevicesQuery());
        });
        \Log::info('ActiveDevices: ' . round((microtime(true) - $t2) * 1000, 2) . 'ms');
        
        $t3 = microtime(true);
        $recentReadings = Cache::remember('dashboard.recent_readings', 5, function () {
            return $this->recentReadingsQueryService->execute(new GetRecentReadingsQuery(20));
        });
        \Log::info('RecentReadings: ' . round((microtime(true) - $t3) * 1000, 2) . 'ms');
        
        $t4 = microtime(true);
        $devicesByGateway = Cache::remember('dashboard.devices_by_gateway', 5, function () {
            return $this->repository->getDevicesByGateway();
        });
        \Log::info('DevicesByGateway: ' . round((microtime(true) - $t4) * 1000, 2) . 'ms');
        
        $t5 = microtime(true);
        $triangulationDevices = Cache::remember('dashboard.triangulation_devices', 5, function () {
            return $this->triangulationQueryService->execute(new GetTriangulationDataQuery(24));
        });
        \Log::info('TriangulationDevices: ' . round((microtime(true) - $t5) * 1000, 2) . 'ms');
        
        // Estado de gateways (cache corto por ser más crítico)
        $t6 = microtime(true);
        $g1Active = Cache::remember('dashboard.g1_active', 2, function () {
            return DB::table('mqtt_readings')
                ->where('topic', '/sur/g1/status')
                ->where('data_timestamp', '>=', now()->subMinutes(5))
                ->exists();
        });
        \Log::info('G1Active: ' . round((microtime(true) - $t6) * 1000, 2) . 'ms');
        
        $t7 = microtime(true);
        $g2Active = Cache::remember('dashboard.g2_active', 2, function () {
            return DB::table('mqtt_readings')
                ->where('topic', '/sur/g2/status')
                ->where('data_timestamp', '>=', now()->subMinutes(5))
                ->exists();
        });
        \Log::info('G2Active: ' . round((microtime(true) - $t7) * 1000, 2) . 'ms');
        
        \Log::info('TOTAL index(): ' . round((microtime(true) - $startTime) * 1000, 2) . 'ms');

        return view('iot-dashboard', compact(
            'stats', 
            'activeDevices', 
            'recentReadings', 
            'devicesByGateway',
            'triangulationDevices',
            'g1Active',
            'g2Active'
        ));
    }

    /**
     * API endpoint para obtener datos en tiempo real
     */
    public function liveData()
    {
        $data = [
            'timestamp' => now()->toIso8601String(),
            'active_devices' => $this->repository->countActiveDevices(),
            'active_beacons' => $this->repository->countActiveBeacons(),
            'readings_last_hour' => \App\Contexts\MqttIngestion\Domain\MqttReading::where('data_timestamp', '>=', now()->subHour())->count(),
        ];

        return response()->json($data);
    }

    /**
     * API endpoint para listar dispositivos activos
     */
    public function devices()
    {
        $devices = $this->activeDevicesQueryService->execute(new GetActiveDevicesQuery());
        
        return response()->json([
            'total' => $devices->count(),
            'devices' => $devices,
        ]);
    }

    /**
     * API endpoint para datos de triangulación en tiempo real
     */
    public function triangulation()
    {
        // Cache de 3 segundos para reducir carga del servidor sin perder "real-time"
        $data = Cache::remember('dashboard.triangulation', 3, function () {
            $triangulationDevices = $this->triangulationQueryService->execute(new GetTriangulationDataQuery(24));
            
            // Estado de gateways
            $g1Active = DB::table('mqtt_readings')
                ->where('topic', '/sur/g1/status')
                ->where('data_timestamp', '>=', now()->subMinutes(5))
                ->exists();
            $g2Active = DB::table('mqtt_readings')
                ->where('topic', '/sur/g2/status')
                ->where('data_timestamp', '>=', now()->subMinutes(5))
                ->exists();

            return [
                'devices' => $triangulationDevices->map(function ($device) {
                    return [
                        'id' => $device->id,
                        'mac_address' => $device->mac_address,
                        'name' => $device->name,
                        'g1_rssi' => $device->g1_rssi,
                        'g2_rssi' => $device->g2_rssi,
                        'g1_last_seen_human' => $device->g1_last_seen?->diffForHumans(),
                        'g2_last_seen_human' => $device->g2_last_seen?->diffForHumans(),
                        'last_seen_human' => $device->last_seen_at?->diffForHumans() ?? 'Nunca',
                    ];
                })->values()->toArray(),
                'g1_active' => $g1Active,
                'g2_active' => $g2Active,
            ];
        });

        return response()->json($data);
    }

    /**
     * API endpoint para obtener historial de lecturas de un dispositivo
     */
    public function deviceReadings($deviceId)
    {
        // Optimizado: limitar a 50 lecturas y join manual para evitar loops
        $readings = \App\Contexts\MqttIngestion\Domain\MqttReading::query()
            ->select('mqtt_readings.*', 'devices.mac_address as gateway_mac')
            ->leftJoin('devices', 'mqtt_readings.gateway_id', '=', 'devices.id')
            ->where('mqtt_readings.device_id', $deviceId)
            ->where('mqtt_readings.data_timestamp', '>=', now()->subHour())
            ->orderBy('mqtt_readings.data_timestamp', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($reading) {
                return [
                    'id' => $reading->id,
                    'topic' => $reading->topic,
                    'rssi' => $reading->specific_data['rssi'] ?? null,
                    'gateway_mac' => $reading->gateway_mac ?? 'N/A',
                    'timestamp' => $reading->data_timestamp->format('Y-m-d H:i:s'),
                    'timestamp_human' => $reading->data_timestamp->diffForHumans(),
                    'raw_data' => $reading->raw_data,
                    'specific_data' => $reading->specific_data,
                ];
            });

        return response()->json([
            'readings' => $readings,
            'total' => $readings->count(),
        ]);
    }
}
