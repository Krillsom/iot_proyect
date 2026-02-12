# Refactorización a DDD + CQRS - Context MqttIngestion

## 🎯 Problema Identificado

La implementación inicial del sistema MQTT **NO seguía la arquitectura DDD + CQRS** establecida en el proyecto:

❌ **Antes**:
- Modelo en `app/Models/MqttReading.php` (arquitectura tradicional Laravel)
- Controlador directo `app/Http/Controllers/DashboardController.php`
- Comando MQTT accediendo directamente al ORM
- Sin separación de Commands/Queries
- Sin eventos de dominio
- Sin Value Objects ni Enums del dominio

## ✅ Solución Implementada

Se refactorizó **completamente** siguiendo el patrón DDD + CQRS usado en los contextos Device y Telemetry.

### Estructura Creada

```
app/Contexts/MqttIngestion/
├── Domain/                          # 🎯 Capa de Dominio (Lógica de negocio)
│   ├── Commands/
│   │   ├── IngestMqttDataCommand.php
│   │   └── IngestBulkMqttDataCommand.php
│   ├── Queries/
│   │   ├── GetActiveDevicesQuery.php
│   │   ├── GetDashboardStatsQuery.php
│   │   ├── GetReadingsByGatewayQuery.php
│   │   └── GetRecentReadingsQuery.php
│   ├── Events/
│   │   ├── MqttDataReceived.php
│   │   ├── BeaconDetected.php
│   │   └── GatewayStatusUpdated.php
│   ├── Enums/
│   │   └── DeviceType.php
│   ├── ValueObjects/
│   │   ├── MacAddress.php
│   │   ├── Topic.php
│   │   └── Rssi.php
│   ├── Repositories/
│   │   └── MqttReadingRepository.php (Interface)
│   └── MqttReading.php              # Modelo de dominio
│
├── Application/                     # ⚙️ Capa de Aplicación (Casos de uso)
│   ├── Command/
│   │   ├── IngestMqttDataCommandService.php
│   │   └── IngestBulkMqttDataCommandService.php
│   ├── Query/
│   │   ├── GetActiveDevicesQueryService.php
│   │   ├── GetDashboardStatsQueryService.php
│   │   ├── GetReadingsByGatewayQueryService.php
│   │   └── GetRecentReadingsQueryService.php
│   └── Dto/
│       └── MqttReadingDto.php
│
├── Infrastructure/                  # 🔧 Capa de Infraestructura (Implementaciones técnicas)
│   └── Persistence/
│       └── MqttReadingRepositoryEloquent.php
│
├── Http/                           # 🌐 Capa HTTP (Presentación)
│   └── Controllers/
│       └── MqttDashboardController.php
│
└── Providers/
    └── MqttIngestionServiceProvider.php
```

## 📊 Comparación Antes/Después

### Antes (Arquitectura tradicional)
```php
// MqttSubscriberCommand.php
$mqttReading = new MqttReading();  // Acceso directo al modelo
$mqttReading->gateway_mac = $gatewayMac;
$mqttReading->device_mac = $reading['mac'];
// ... 20 líneas más
$mqttReading->save();  // Guardado directo
```

```php
// DashboardController.php
public function index()
{
    $stats = [
        'total_readings' => MqttReading::count(),  // Queries mezcladas con lógica
        'active_devices' => MqttReading::select('device_mac')->distinct()->count(),
    ];
    // ... más queries directas
}
```

### Después (DDD + CQRS)

#### Write Side (Commands)
```php
// MqttSubscriberCommand.php
$command = new IngestBulkMqttDataCommand(
    topic: $topic,
    payloads: $readings,
    gatewayMac: $gatewayMac
);

$savedCount = $this->ingestService->execute($command);  // ⚡ CQRS
```

```php
// IngestBulkMqttDataCommandService.php
public function execute(IngestBulkMqttDataCommand $command): int
{
    // Lógica de negocio encapsulada
    $readings = $this->transformPayloads($command->payloads);
    return $this->repository->saveBulk($readings);
}
```

#### Read Side (Queries)
```php
// MqttDashboardController.php
public function index()
{
    // Usa Query Services (CQRS)
    $stats = $this->statsQueryService->execute(new GetDashboardStatsQuery());
    $devices = $this->activeDevicesQueryService->execute(new GetActiveDevicesQuery());
    $recentReadings = $this->recentReadingsQueryService->execute(new GetRecentReadingsQuery(20));
}
```

## 🎯 CQRS en Acción

### Write Operations (Command Side)

**Propósito**: Modificar estado del sistema

```
MQTT Message
     ↓
IngestBulkMqttDataCommand
     ↓
IngestBulkMqttDataCommandService
     ↓
MqttReadingRepository (interface)
     ↓
MqttReadingRepositoryEloquent (implementation)
     ↓
Database INSERT
     ↓
Domain Events (MqttDataReceived, BeaconDetected, etc.)
```

### Read Operations (Query Side)

**Propósito**: Obtener datos sin modificar estado

```
HTTP Request
     ↓
GetDashboardStatsQuery
     ↓
GetDashboardStatsQueryService
     ↓
MqttReadingRepository (interface)
     ↓
MqttReadingRepositoryEloquent (implementation)
     ↓
Optimized SELECT queries
     ↓
DTO Response
```

## 🏗️ Conceptos DDD Aplicados

### 1. **Bounded Context**
MqttIngestion es un contexto acotado independiente con:
- Su propio lenguaje ubicuo (Topic, Rssi, MacAddress)
- Sus propias reglas de negocio
- Interfaces bien definidas

### 2. **Entities**
```php
class MqttReading extends Model  // Entity con identidad (id)
```

### 3. **Value Objects**
```php
final class MacAddress       // Inmutable, con validación
final class Topic           // Con lógica de negocio
final class Rssi            // Métodos: isStrong(), isMedium(), isWeak()
```

### 4. **Domain Events**
```php
event(new BeaconDetected($beaconMac, $gatewayMac, $rssi, $occurredAt));
event(new GatewayStatusUpdated($gatewayMac, $freeMemory, $load, $occurredAt));
```

### 5. **Repository Pattern**
```php
interface MqttReadingRepository  // Contrato del dominio
{
    public function save(array $data): MqttReading;
    public function saveBulk(array $readings): int;
}

class MqttReadingRepositoryEloquent implements MqttReadingRepository  // Infra
```

### 6. **Application Services**
```php
class IngestBulkMqttDataCommandService  // Orquesta caso de uso
{
    public function __construct(
        private readonly MqttReadingRepository $repository  // Inyección de dependencias
    ) {}
}
```

### 7. **Dependency Inversion**
```php
// ServiceProvider
$this->app->bind(
    MqttReadingRepository::class,        // Abstracción
    MqttReadingRepositoryEloquent::class // Implementación
);
```

## 📦 Archivos Modificados/Eliminados

### ✅ Creados (25 archivos nuevos)
- Domain: 3 Commands, 4 Queries, 3 Events, 1 Enum, 3 ValueObjects, 1 Repository, 1 Model
- Application: 2 CommandServices, 4 QueryServices, 1 DTO
- Infrastructure: 1 RepositoryImpl
- Http: 1 Controller
- Providers: 1 ServiceProvider
- 1 README.md con documentación completa

### 🗑️ Eliminados
- `app/Models/MqttReading.php` (arquitectura tradicional)
- `app/Http/Controllers/DashboardController.php` (sin CQRS)

### ✏️ Modificados
- `app/Console/Commands/MqttSubscriberCommand.php` (ahora usa CQRS)
- `routes/web.php` (apunta al nuevo controlador)
- `bootstrap/providers.php` (registra MqttIngestionServiceProvider)

## 🎓 Beneficios Obtenidos

### ✅ Separación de Responsabilidades
- **Commands**: Solo escriben, optimizados para transacciones
- **Queries**: Solo leen, optimizados para performance
- **Domain**: Lógica de negocio pura, testeable

### ✅ Mantenibilidad
- Cada caso de uso es una clase específica
- Fácil entender qué hace cada componente
- Cambios en queries no afectan commands

### ✅ Testabilidad
```php
// Test unitario
$mockRepo = Mockery::mock(MqttReadingRepository::class);
$service = new IngestMqttDataCommandService($mockRepo);
$command = new IngestMqttDataCommand(...);
$service->execute($command);
```

### ✅ Escalabilidad
- Commands pueden usar queues
- Queries pueden usar read replicas
- Events permiten integración con otros contextos

### ✅ Consistencia
- Device, Telemetry y MqttIngestion siguen **la misma arquitectura**
- Mismo patrón en todo el proyecto
- Fácil agregar nuevos contextos

## 🚀 Cómo Usar

### 1. Comando MQTT (ahora con CQRS)
```bash
php artisan mqtt:subscribe \
    --host=IP_184 \
    --port=1883 \
    --username=usuario \
    --password=password \
    --topic="/sur/g2/status"
```

Verás en consola:
```
🔌 Conectando a MQTT broker: IP_184:1883
📡 Topic: /sur/g2/status
🏗️  Usando arquitectura DDD + CQRS      ← ¡NUEVO!
✅ Conectado exitosamente al broker MQTT
⏳ Esperando mensajes...

📩 Mensaje recibido en topic: /sur/g2/status
💾 Guardados 3 registros | Gateway: AC233FC03641 | ⚡ CQRS  ← ¡NUEVO!
```

### 2. Dashboard Web
```bash
php artisan serve
# http://localhost:8000/dashboard
```

Ahora usa Query Services CQRS internamente.

### 3. API Endpoints
```bash
GET /api/dashboard/live      # Stats en tiempo real (CQRS)
GET /api/dashboard/devices   # Lista de dispositivos (CQRS)
```

## 📚 Documentación

- **Detalle completo**: Ver `app/Contexts/MqttIngestion/README.md`
- **Guía de usuario**: Ver `MQTT_IOT_GUIDE.md`

## ✨ Estado del Proyecto

### Contextos Implementados

| Contexto | Arquitectura | Estado |
|----------|-------------|---------|
| Device | ✅ DDD + CQRS | Completo |
| Telemetry | ✅ DDD + CQRS | Completo |
| **MqttIngestion** | ✅ **DDD + CQRS** | **Completo (Refactorizado)** |

### Próximos Pasos

- Integrar contextos mediante Domain Events
- Agregar tests unitarios con PHPUnit/Pest
- Implementar Event Listeners entre contextos
- Agregar contexto Alert (siguiente sprint)

---

## 🎉 Resumen

✅ **Sistema MQTT ahora sigue completamente la arquitectura DDD + CQRS**  
✅ **Consistent con Device y Telemetry**  
✅ **Commands y Queries separados**  
✅ **Domain Events implementados**  
✅ **Value Objects con lógica de negocio**  
✅ **Repository Pattern con interface**  
✅ **Dependency Injection configurada**  
✅ **Testeable y escalable**

🚀 **El proyecto ahora tiene 3 contextos bounded siguiendo las mejores prácticas de DDD + CQRS**
