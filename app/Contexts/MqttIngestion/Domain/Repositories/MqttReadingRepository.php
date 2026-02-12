<?php

namespace App\Contexts\MqttIngestion\Domain\Repositories;

use App\Contexts\MqttIngestion\Domain\MqttReading;
use Illuminate\Support\Collection;

interface MqttReadingRepository
{
    /**
     * Guardar una lectura MQTT
     */
    public function save(array $data): MqttReading;

    /**
     * Guardar múltiples lecturas en bulk
     */
    public function saveBulk(array $readings): int;

    /**
     * Obtener dispositivos activos únicos
     */
    public function getActiveDevices(int $hoursLimit = 24): Collection;

    /**
     * Obtener lecturas por gateway
     */
    public function getByGateway(string $gatewayMac, int $limit = 100): Collection;

    /**
     * Obtener últimas lecturas
     */
    public function getRecentReadings(int $limit = 20): Collection;

    /**
     * Obtener estadísticas del dashboard
     */
    public function getDashboardStats(): array;

    /**
     * Contar dispositivos activos
     */
    public function countActiveDevices(): int;

    /**
     * Contar beacons activos
     */
    public function countActiveBeacons(): int;

    /**
     * Contar gateways activos
     */
    public function countActiveGateways(): int;

    /**
     * Obtener dispositivos agrupados por gateway
     */
    public function getDevicesByGateway(): Collection;

    /**
     * Obtener datos de triangulación (RSSI por gateway)
     */
    public function getTriangulationData(int $hoursLimit = 24): Collection;
}
