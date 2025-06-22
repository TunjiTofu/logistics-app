<?php

namespace App\Repositories\Shipment;

use App\Models\Shipment;
use App\Utility\Constants;
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

    public function getUserShipments($userId, array $data)
    {
        try {
            $query= Shipment::where('created_by', $userId)->statusFilter();
            return $query->latest()->paginate($data['limit'] ?? Constants::RECORD_LIMIT_PER_PAGE);
        } catch (QueryException $e) {
            Log::error("Shipment creation query failed: {$e->getMessage()}", [$e]);
            return null;
        }
    }
}
