<?php

namespace App\Contexts\MqttIngestion\Application\Command;

use App\Contexts\MqttIngestion\Domain\Commands\IngestMqttDataCommand;
use App\Contexts\MqttIngestion\Domain\Repositories\MqttReadingRepository;
use App\Contexts\MqttIngestion\Domain\Events\MqttDataReceived;
use App\Contexts\MqttIngestion\Domain\Events\BeaconDetected;
use App\Contexts\MqttIngestion\Domain\Events\GatewayStatusUpdated;
use App\Contexts\MqttIngestion\Domain\MqttReading;
use Carbon\Carbon;

class IngestMqttDataCommandService
{
    public function __construct(
        private readonly MqttReadingRepository $repository
    ) {}

    public function execute(IngestMqttDataCommand $command): MqttReading
    {
        $payload = $command->payload;

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

        $data = [
            'gateway_mac' => $command->gatewayMac,
            'topic' => $command->topic,
            'device_mac' => $payload['mac'],
            'device_type' => $payload['type'],
            'device_name' => $payload['bleName'] ?? null,
            'specific_data' => $specificData,  // ✅ JSON con datos específicos
            'raw_data' => $payload,
            'data_timestamp' => isset($payload['timestamp']) 
                ? Carbon::parse($payload['timestamp']) 
                : now(),
        ];

        $reading = $this->repository->save($data);

        // Disparar eventos de dominio
        event(new MqttDataReceived(
            topic: $command->topic,
            data: $payload,
            gatewayMac: $command->gatewayMac,
            occurredAt: new \DateTimeImmutable()
        ));

        if ($payload['type'] === 'iBeacon') {
            event(new BeaconDetected(
                beaconMac: $payload['mac'],
                gatewayMac: $command->gatewayMac,
                rssi: $payload['rssi'] ?? 0,
                occurredAt: new \DateTimeImmutable()
            ));
        }

        if ($payload['type'] === 'Gateway') {
            event(new GatewayStatusUpdated(
                gatewayMac: $payload['mac'],
                freeMemory: $payload['gatewayFree'] ?? 0,
                load: $payload['gatewayLoad'] ?? 0.0,
                occurredAt: new \DateTimeImmutable()
            ));
        }

        return $reading;
    }
}
