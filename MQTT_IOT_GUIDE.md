# Sistema IoT MQTT - Guía de Uso

## 📋 Descripción

Sistema para recibir datos desde gateways IoT vía MQTT, almacenarlos en MySQL y visualizarlos en un dashboard en tiempo real.

## 🚀 Características Implementadas

### ✅ 1. Consumo de Datos MQTT
- Conexión a broker MQTT (puerto 1883)
- Suscripción a topics configurables
- Procesamiento de mensajes JSON con Gateway + iBeacons
- Almacenamiento automático en base de datos

### ✅ 2. Base de Datos
Tabla `mqtt_readings` con:
- **Gateway**: MAC, free memory, load
- **iBeacon**: MAC, UUID, major, minor, RSSI, TX Power, battery
- JSON completo (raw_data)
- Timestamps
- Índices optimizados para queries rápidas

### ✅ 3. Dashboard Web
- 📊 **Estadísticas en tiempo real**:
  - Total dispositivos activos
  - iBeacons activos
  - Gateways activos
  - Lecturas última hora
- 📡 **Dispositivos por Gateway**
- 📋 **Lista de dispositivos activos** con última vez vista
- 📝 **Lecturas recientes** con RSSI
- 🔄 **Auto-refresh cada 10 segundos**

## 🔧 Configuración Inicial

### 1. Variables de Entorno

Asegúrate de que MySQL esté configurado en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iot_schema
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 2. Ejecutar Migraciones

Si aún no lo has hecho:

```bash
php artisan migrate
```

## 📡 Conectar al MQTT Broker

### Comando Base

```bash
php artisan mqtt:subscribe \
    --host=TU_IP_DEL_184 \
    --port=1883 \
    --username=tu_usuario \
    --password=tu_password \
    --topic="/sur/g2/status"
```

### Ejemplo con Datos Reales

Según tu mensaje MQTT en el servidor 184:

```bash
php artisan mqtt:subscribe \
    --host=IP_DEL_SERVIDOR_184 \
    --port=1883 \
    --username=xxxxx \
    --password=xxxxx \
    --topic="/sur/g2/status"
```

### Escuchar Múltiples Topics

Para escuchar todos los topics:

```bash
php artisan mqtt:subscribe \
    --host=IP_DEL_184 \
    --port=1883 \
    --username=xxxxx \
    --password=xxxxx \
    --topic="#"
```

Para topics específicos por gateway:

```bash
# Solo gateway 2
--topic="/sur/g2/status"

# Todos los gateways de "sur"
--topic="/sur/+/status"

# Todos los gateways
--topic="+/+/status"
```

## 🖥️ Acceder al Dashboard

1. **Iniciar servidor Laravel**:
   ```bash
   php artisan serve
   ```

2. **Abrir navegador**:
   ```
   http://localhost:8000/dashboard
   ```

3. **Login**: Usa tus credenciales de Breeze

## 📊 Formato de Datos Esperado

El sistema espera mensajes MQTT en formato JSON array:

```json
[
  {
    "timestamp": "2026-02-09T00:13:53.668Z",
    "type": "Gateway",
    "mac": "AC233FC03641",
    "gatewayFree": 89,
    "gatewayLoad": 0.029999999999999999
  },
  {
    "timestamp": "2026-02-09T00:13:53.667Z",
    "type": "iBeacon",
    "mac": "C3000025EF6A",
    "bleName": "",
    "ibeaconUuid": "E2C56DB5DFFB48D2B060D0F5A71096E0",
    "ibeaconMajor": 0,
    "ibeaconMinor": 0,
    "rssi": -65,
    "ibeaconTxPower": -59,
    "battery": 0
  }
]
```

## 🔄 Proceso Completo

### 1. Iniciar Consumidor MQTT (Terminal 1)

```bash
php artisan mqtt:subscribe --host=IP_184 --port=1883 --username=xxxxx --password=xxxxx --topic="/sur/g2/status"
```

Verás output como:
```
🔌 Conectando a MQTT broker: IP_184:1883
📡 Topic: /sur/g2/status
✅ Conectado exitosamente al broker MQTT
⏳ Esperando mensajes... (Ctrl+C para detener)

📩 Mensaje recibido en topic: /sur/g2/status
💾 Guardados 3 registros | Gateway: AC233FC03641
```

### 2. Iniciar Servidor Web (Terminal 2)

```bash
php artisan serve
```

### 3. Abrir Dashboard

Navega a `http://localhost:8000/dashboard` y verás:
- Conteo de dispositivos actualizándose en tiempo real
- Lista de iBeacons detectados
- RSSI de cada dispositivo
- Lecturas recientes

## 🛠️ Comandos Útiles

### Ver todos los dispositivos activos
```bash
php artisan tinker
>>> App\Models\MqttReading::getActiveDevices();
```

### Limpiar lecturas antiguas (más de 7 días)
```bash
php artisan tinker
>>> App\Models\MqttReading::where('data_timestamp', '<', now()->subDays(7))->delete();
```

### Contar lecturas por tipo
```bash
php artisan tinker
>>> App\Models\MqttReading::selectRaw('device_type, COUNT(*) as count')->groupBy('device_type')->get();
```

## 📚 API Endpoints

### Obtener datos en tiempo real
```bash
GET /api/dashboard/live
```

Respuesta:
```json
{
  "timestamp": "2026-02-09T01:23:45.678Z",
  "active_devices": 15,
  "active_beacons": 12,
  "readings_last_minute": 45,
  "latest_readings": [...]
}
```

### Listar dispositivos activos
```bash
GET /api/dashboard/devices
```

Respuesta:
```json
{
  "total": 15,
  "devices": [
    {
      "device_mac": "C3000025EF6A",
      "device_type": "iBeacon",
      "device_name": null,
      "last_seen": "2026-02-09 01:23:45",
      "gateway_mac": "AC233FC03641"
    }
  ]
}
```

## 🐛 Troubleshooting

### Error: Connection refused al MQTT
- Verifica que el firewall permita conexión al puerto 1883
- Confirma la IP/hostname del servidor MQTT
- Prueba conectividad: `telnet IP_184 1883`

### No se guardan datos en BD
- Verifica que MySQL esté corriendo
- Revisa `.env` con credenciales correctas
- Ejecuta `php artisan migrate:status`

### Dashboard no muestra dispositivos
- Asegúrate de que el comando `mqtt:subscribe` esté corriendo
- Verifica que haya datos en la tabla: `SELECT COUNT(*) FROM mqtt_readings;`
- Revisa logs: `tail -f storage/logs/laravel.log`

## 📈 Próximos Pasos (Opcional)

- [ ] Agregar alertas cuando dispositivos se desconecten
- [ ] Implementar gráficas históricas de RSSI
- [ ] Exportar datos a CSV/Excel
- [ ] WebSockets para dashboard en tiempo real sin polling
- [ ] Mapa de ubicación de dispositivos
- [ ] Notificaciones push/email

## 📝 Notas Importantes

1. **El comando `mqtt:subscribe` debe estar corriendo continuamente** para recibir datos
2. **Considera usar Supervisor** para mantener el proceso MQTT activo incluso si se cae
3. **La tabla puede crecer rápido**: implementa limpieza de datos antiguos según tu necesidad
4. **RSSI**: Valores típicos: -30 a -40 (excelente), -50 a -60 (bueno), -70+ (débil)

---

## 🎯 Resumen Rápido

```bash
# Terminal 1: Consumir MQTT
php artisan mqtt:subscribe --host=IP_184 --port=1883 --username=xxxxx --password=xxxxx --topic="/sur/g2/status"

# Terminal 2: Servidor Laravel
php artisan serve

# Navegador: Dashboard
http://localhost:8000/dashboard
```

¡Listo! Ya tienes tu sistema IoT funcionando 🚀
