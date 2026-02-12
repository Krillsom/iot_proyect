<?php

namespace App\Contexts\MqttIngestion\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contexts\MqttIngestion\Domain\Repositories\MqttReadingRepository;
use App\Contexts\MqttIngestion\Infrastructure\Persistence\MqttReadingRepositoryEloquent;

class MqttIngestionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar repositorio con su implementación
        $this->app->bind(
            MqttReadingRepository::class,
            MqttReadingRepositoryEloquent::class
        );

        // Registrar Command Services
        $this->app->singleton(
            \App\Contexts\MqttIngestion\Application\Command\IngestMqttDataCommandService::class
        );
        $this->app->singleton(
            \App\Contexts\MqttIngestion\Application\Command\IngestBulkMqttDataCommandService::class
        );

        // Registrar Query Services
        $this->app->singleton(
            \App\Contexts\MqttIngestion\Application\Query\GetActiveDevicesQueryService::class
        );
        $this->app->singleton(
            \App\Contexts\MqttIngestion\Application\Query\GetDashboardStatsQueryService::class
        );
        $this->app->singleton(
            \App\Contexts\MqttIngestion\Application\Query\GetRecentReadingsQueryService::class
        );
        $this->app->singleton(
            \App\Contexts\MqttIngestion\Application\Query\GetReadingsByGatewayQueryService::class
        );
        $this->app->singleton(
            \App\Contexts\MqttIngestion\Application\Query\GetTriangulationDataQueryService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Cargar rutas si existen
        if (file_exists(__DIR__ . '/../Http/routes.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        }
    }
}
