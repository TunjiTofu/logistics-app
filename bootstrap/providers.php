<?php

use App\Providers\GeolocationServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\ShipmentServiceProvider::class,
    GeolocationServiceProvider::class
];
