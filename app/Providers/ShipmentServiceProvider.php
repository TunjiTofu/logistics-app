<?php

namespace App\Providers;

use App\Repositories\Shipment\ShipmentRepository;
use App\Repositories\Shipment\ShipmentRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ShipmentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ShipmentRepositoryInterface::class, ShipmentRepository::class
        );
    }
}
