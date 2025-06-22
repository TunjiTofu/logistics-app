<?php

namespace App\Repositories\Shipment;

use App\Models\Shipment;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    public function createShipment(array $data): ?Shipment
    {
        try {
            return Shipment::create($data);
        } catch (QueryException $e) {
            Log::error("Shipment creation query failed: {$e->getMessage()}", [$e]);
            return null;
        }
    }
}
