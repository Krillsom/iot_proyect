<?php

namespace App\Contexts\Device\Http\Controllers;

use App\Contexts\Device\Application\Command\DeleteDeviceCommandService;
use App\Contexts\Device\Application\Command\RegisterDeviceCommandService;
use App\Contexts\Device\Application\Command\UpdateDeviceConfigCommandService;
use App\Contexts\Device\Application\Command\UpdateDeviceStatusCommandService;
use App\Contexts\Device\Application\Query\GetAllDevicesQueryService;
use App\Contexts\Device\Application\Query\GetDeviceQueryService;
use App\Contexts\Device\Domain\Commands\DeleteDeviceCommand;
use App\Contexts\Device\Domain\Commands\RegisterDeviceCommand;
use App\Contexts\Device\Domain\Commands\UpdateDeviceConfigCommand;
use App\Contexts\Device\Domain\Commands\UpdateDeviceStatusCommand;
use App\Contexts\Device\Domain\Enums\DeviceStatus;
use App\Contexts\Device\Domain\Enums\DeviceType;
use App\Contexts\Device\Domain\Enums\SensorType;
use App\Contexts\Device\Domain\Queries\GetDeviceQuery;
use App\Contexts\Device\Http\Requests\RegisterDeviceRequest;
use App\Contexts\Device\Http\Requests\UpdateDeviceRequest;
use App\Contexts\Device\Http\Resources\DeviceResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeviceController extends Controller
{
    public function __construct(
        private RegisterDeviceCommandService $registerDeviceService,
        private UpdateDeviceStatusCommandService $updateDeviceStatusService,
        private UpdateDeviceConfigCommandService $updateDeviceConfigService,
        private DeleteDeviceCommandService $deleteDeviceService,
        private GetAllDevicesQueryService $getAllDevicesService,
        private GetDeviceQueryService $getDeviceService,
    ) {}

    /**
     * Display a listing of devices.
     */
    public function index(): AnonymousResourceCollection
    {
        $devices = $this->getAllDevicesService->execute();
        return DeviceResource::collection($devices);
    }

    /**
     * Store a newly created device.
     */
    public function store(RegisterDeviceRequest $request): JsonResponse
    {
        try {
            $command = new RegisterDeviceCommand(
                name: $request->name,
                type: $request->enum('type', DeviceType::class),
                sensorType: $request->enum('sensor_type', SensorType::class),
                macAddress: $request->mac_address,
                ipAddress: $request->ip_address,
                parentId: $request->parent_id,
                metadata: $request->metadata,
            );

            $deviceDto = $this->registerDeviceService->execute($command);

            return response()->json([
                'message' => 'Dispositivo registrado exitosamente',
                'data' => $deviceDto->toArray(),
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => 'Error al registrar dispositivo',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified device.
     */
    public function show(string $uuid): JsonResponse
    {
        try {
            $query = new GetDeviceQuery($uuid);
            $deviceDto = $this->getDeviceService->execute($query);

            if (!$deviceDto) {
                return response()->json([
                    'message' => 'Dispositivo no encontrado',
                ], 404);
            }

            return response()->json([
                'data' => $deviceDto->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener dispositivo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified device.
     */
    public function update(UpdateDeviceRequest $request, string $uuid): JsonResponse
    {
        try {
            // Actualizar configuración
            if ($request->has(['name', 'ip_address', 'metadata'])) {
                $command = new UpdateDeviceConfigCommand(
                    deviceId: $uuid,
                    name: $request->name,
                    ipAddress: $request->ip_address,
                    metadata: $request->metadata,
                );
                $deviceDto = $this->updateDeviceConfigService->execute($command);
            }

            // Actualizar estado
            if ($request->has('status')) {
                $command = new UpdateDeviceStatusCommand(
                    deviceId: $uuid,
                    status: DeviceStatus::from($request->status),
                );
                $deviceDto = $this->updateDeviceStatusService->execute($command);
            }

            return response()->json([
                'message' => 'Dispositivo actualizado exitosamente',
                'data' => $deviceDto->toArray(),
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => 'Error al actualizar dispositivo',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified device.
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $command = new DeleteDeviceCommand($uuid);
            $deleted = $this->deleteDeviceService->execute($command);

            if ($deleted) {
                return response()->json([
                    'message' => 'Dispositivo eliminado exitosamente',
                ], 200);
            }

            return response()->json([
                'message' => 'No se pudo eliminar el dispositivo',
            ], 500);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => 'Error al eliminar dispositivo',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
