<?php

namespace App\Console\Commands;

use App\Contexts\MqttIngestion\Application\Command\IngestBulkMqttDataCommandService;
use App\Contexts\MqttIngestion\Domain\Commands\IngestBulkMqttDataCommand;
use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;

class MqttSubscriberCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:subscribe 
                            {--host= : MQTT broker host}
                            {--port= : MQTT broker port}
                            {--username= : MQTT username}
                            {--password= : MQTT password}
                            {--topic= : MQTT topic to subscribe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to MQTT broker and store readings in database using DDD+CQRS';

    public function __construct(
        private readonly IngestBulkMqttDataCommandService $ingestService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Usar valores de configuración si no se pasan por CLI
        $host = $this->option('host') ?? config('mqtt.host');
        $port = (int) ($this->option('port') ?? config('mqtt.port'));
        $username = $this->option('username') ?? config('mqtt.username');
        $password = $this->option('password') ?? config('mqtt.password');
        $topic = $this->option('topic') ?? config('mqtt.topic');

        $this->info("🔌 Conectando a MQTT broker: {$host}:{$port}");
        $this->info("📡 Topic: {$topic}");
        $this->info("🏗️  Usando arquitectura DDD + CQRS");

        try {
            $mqtt = new MqttClient($host, $port, uniqid('laravel_mqtt_'));

            $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)
                ->setUsername($username)
                ->setPassword($password)
                ->setKeepAliveInterval(config('mqtt.keep_alive_interval', 60))
                ->setLastWillTopic(config('mqtt.last_will_topic', 'iot/disconnect'))
                ->setLastWillMessage(config('mqtt.last_will_message', 'Laravel MQTT client disconnected'))
                ->setLastWillQualityOfService(config('mqtt.last_will_qos', 0));

            $mqtt->connect($connectionSettings, true);

            $this->info("✅ Conectado exitosamente al broker MQTT");
            $this->info("⏳ Esperando mensajes... (Ctrl+C para detener)");

            $mqtt->subscribe($topic, function (string $receivedTopic, string $message) {
                $this->processMessage($receivedTopic, $message);
            }, 0);

            $mqtt->loop(true);

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Process incoming MQTT message using CQRS Command
     */
    protected function processMessage(string $topic, string $message): void
    {
        try {
            $this->info("\n📩 Mensaje recibido en topic: {$topic}");
            
            $data = json_decode($message, true);
            
            if (!is_array($data)) {
                $this->warn("⚠️  Mensaje no es un JSON válido");
                return;
            }

            // El mensaje puede ser un array de objetos o un solo objeto
            $readings = isset($data[0]) ? $data : [$data];
            
            // Identificar el gateway
            $gatewayMac = $this->extractGatewayMac($readings, $topic);

            // Usar CQRS Command para ingestar los datos
            $command = new IngestBulkMqttDataCommand(
                topic: $topic,
                payloads: $readings,
                gatewayMac: $gatewayMac
            );

            $savedCount = $this->ingestService->execute($command);

            $this->line("💾 Guardados {$savedCount} registros | Gateway: {$gatewayMac} | ⚡ CQRS");

        } catch (\Exception $e) {
            $this->error("❌ Error procesando mensaje: " . $e->getMessage());
            $this->error("   Stack: " . $e->getTraceAsString());
        }
    }

    /**
     * Extract gateway MAC from readings or topic
     */
    private function extractGatewayMac(array $readings, string $topic): string
    {
        // Buscar el primer Gateway en las lecturas
        foreach ($readings as $reading) {
            if (($reading['type'] ?? '') === 'Gateway') {
                return $reading['mac'];
            }
        }

        // Si no hay Gateway en los datos, extraer del topic
        if (preg_match('/\/([^\/]+)\/status/', $topic, $matches)) {
            return strtoupper(str_replace('/', '_', $matches[1]));
        }

        return 'UNKNOWN';
    }
}
