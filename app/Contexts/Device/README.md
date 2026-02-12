# Context: Device

Este contexto maneja toda la lógica relacionada con la gestión de dispositivos IoT.

## Estructura

```
Device/
├── Application/              # Capa de aplicación (CQRS)
│   ├── Command/             # Servicios de comandos (Write)
│   ├── Query/               # Servicios de queries (Read)
│   └── Dto/                 # Data Transfer Objects
├── Domain/                  # Capa de dominio
│   ├── Commands/            # Comandos del dominio
│   ├── Queries/             # Queries del dominio
│   ├── Events/              # Eventos del dominio
│   ├── Enums/               # Enumeraciones
│   ├── ValueObjects/        # Value Objects
│   ├── Repositories/        # Interfaces de repositorios
│   └── Device.php           # Modelo de dominio
├── Http/                    # Capa HTTP
│   ├── Controllers/         # Controladores
│   ├── Requests/            # Form Requests
│   ├── Resources/           # API Resources
│   └── routes.php           # Rutas del contexto
├── Infrastructure/          # Capa de infraestructura
│   └── Persistence/         # Implementaciones de persistencia
└── Providers/               # Service Providers
```

## Tipos de Dispositivos

- **Sensor**: Dispositivos que capturan datos (motion, GPS, temperatura, etc.)
- **Camera**: Cámaras de video
- **Gateway**: Dispositivos intermediarios que conectan sensores
- **Edge**: Dispositivos edge computing que pueden procesar localmente

## Estados de Dispositivos

- **online**: Dispositivo conectado y activo
- **offline**: Dispositivo desconectado
- **maintenance**: En mantenimiento
- **error**: Con errores
- **inactive**: Inactivo

## API Endpoints

```
GET    /api/devices           # Listar todos los dispositivos
POST   /api/devices           # Registrar nuevo dispositivo
GET    /api/devices/{uuid}    # Obtener dispositivo específico
PUT    /api/devices/{uuid}    # Actualizar dispositivo
DELETE /api/devices/{uuid}    # Eliminar dispositivo
```

## Ejemplo de Uso

### Registrar un sensor de movimiento:

```bash
POST /api/devices
{
  "name": "Sensor Entrada Principal",
  "type": "sensor",
  "sensor_type": "motion",
  "mac_address": "AA:BB:CC:DD:EE:FF",
  "ip_address": "192.168.1.100",
  "parent_id": "gateway-uuid",
  "metadata": {
    "location": "Entrada",
    "floor": 1
  }
}
```

### Registrar un gateway:

```bash
POST /api/devices
{
  "name": "Gateway Principal",
  "type": "gateway",
  "mac_address": "11:22:33:44:55:66",
  "ip_address": "192.168.1.50"
}
```

## Eventos del Dominio

- `DeviceRegistered`: Se dispara cuando se registra un nuevo dispositivo
- `DeviceStatusChanged`: Se dispara cuando cambia el estado de un dispositivo
- `DeviceConfigUpdated`: Se dispara cuando se actualiza la configuración
- `DeviceDeleted`: Se dispara cuando se elimina un dispositivo

## Jerarquía de Dispositivos

Los dispositivos pueden organizarse jerárquicamente:

```
Edge Device
  └─ Gateway 1
      ├─ Sensor Motion 1
      ├─ Sensor GPS 1
      └─ Camera 1
  └─ Gateway 2
      └─ Sensor Temperature 1
```
