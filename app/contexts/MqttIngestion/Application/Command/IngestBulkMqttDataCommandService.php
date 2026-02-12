<?php

namespace App\Contexts\MqttIngestion\Application\Command;

use App\Contexts\MqttIngestion\Domain\Commands\IngestBulkMqttDataCommand;
use App\Contexts\MqttIngestion\Domain\Repositories\MqttReadingRepository;
use App\Contexts\MqttIngestion\Domain\Events\DeviceDetected;
use App\Contexts\Device\Domain\Repositories\DeviceRepository;
use Carbon\Carbon;

class IngestBulkMqttDataCommandService
{
    public function __construct(
        private readonly MqttReadingRepository $repository,
        private readonly DeviceRepository $deviceRepository
    ) {}

    public function execute(IngestBulkMqttDataCommand $command): int
    {
        $readings = [];
        $now = now();

        // 1️⃣ EMITIR EVENTOS: Auto-registrar gateway (SÍNCRONO)
        DeviceDetected::dispatch(
            $command->gatewayMac,
            'Gateway',
            $command->gatewayMac
        );

        // 2️⃣ EMITIR EVENTOS: Auto-registrar cada dispositivo (SÍNCRONO)
        foreach ($command->payloads as $payload) {
            DeviceDetected::dispatch(
                $payload['mac'],
                $payload['type'],
                $payload['bleName'] ?? $payload['mac'],
                $command->gatewayMac
            );
        }

        // 3️⃣ OBTENER IDs: Dispositivos ya están registrados por listeners
        $gateway = $this->deviceRepository->findByMacAddress($command->gatewayMac);

        foreach ($command->payloads as $payload) {
            $device = $this->deviceRepository->findByMacAddress($payload['mac']);

            // Extraer datos específicos según el tipo
            $specificData = [];
            
            if ($payload['type'] === 'iBeacon') {
                $specificData = [
                    'ibeacon_uuid' => $payload['ibeaconUuid'] ?? null,
                    'ibeacon_major' => $payload['ibeaconMajor'] ?? null,
                    'ibeacon_minor' => $payload['ibeaconMinor'] ?? null,
                    'rssi' => $payload['rssi'] ?? null,
                    'ibeacon_tx_power' => $payload['ibeaconTxPower'] ?? null,
                    'battery' => $payload['battery'] ?? null,
                ];
            } elseif ($payload['type'] === 'Gateway') {
                $specificData = [
                    'gateway_free' => $payload['gatewayFree'] ?? null,
                    'gateway_load' => $payload['gatewayLoad'] ?? null,
                ];
            }

            // 4️⃣ CONSTRUIR LECTURAS: Con FK válidas
            $data = [
                'device_id' => $device->id,          // ✅ FK normalizada
                'gateway_id' => $gateway->id,        // ✅ FK normalizada
                'topic' => $command->topic,
                'specific_data' => json_encode($specificData),
                'raw_data' => json_encode($payload),
                'data_timestamp' => isset($payload['timestamp']) 
                    ? Carbon::parse($payload['timestamp']) 
                    : $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $readings[] = $data;
        }

        return $this->repository->saveBulk($readings);
    }
}
