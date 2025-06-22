<?php

namespace App\Services\Geolocation;

interface GeolocationServiceInterface
{
    public function getCoordinates(string $address): ?array;
}
