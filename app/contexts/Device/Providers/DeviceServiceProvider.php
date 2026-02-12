<?php

namespace App\Contexts\Device\Providers;

use App\Contexts\Device\Domain\Repositories\DeviceRepository;
use App\Contexts\Device\Infrastructure\Persistence\DeviceRepositoryEloquent;
use App\Contexts\Device\Application\Listeners\AutoRegisterDeviceListener;
use App\Contexts\MqttIngestion\Domain\Events\DeviceDetected;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class DeviceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar el binding del repositorio
        $this->app->bind(
            DeviceRepository::class,
            DeviceRepositoryEloquent::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Cargar las rutas del contexto
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Cargar las migraciones del contexto
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // ✅ Registrar listeners de Domain Events (SÍNCRONO)
        Event::listen(
            DeviceDetected::class,
            AutoRegisterDeviceListener::class
        );
    }
}
