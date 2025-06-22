<?php

namespace App\Providers;

use App\Repositories\Shipment\ShipmentRepository;
use App\Repositories\Shipment\ShipmentRepositoryInterface;
use App\Services\Geolocation\GeolocationService;
use App\Services\Geolocation\GeolocationServiceInterface;
use Illuminate\Support\ServiceProvider;

class GeolocationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            GeolocationServiceInterface::class, GeolocationService::class
        );
    }
}
