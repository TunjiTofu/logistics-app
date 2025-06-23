<?php

namespace App\Repositories\Shipment;

use App\DTOs\Admin\UpdateShipmentStatusDTO;
use App\Models\Shipment;
use App\Utility\Constants;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    /**
     * @param int $id
     * @return Shipment|null
     */
    public function findShipmentById(int $id): ?Shipment
    {
        try {
            return Shipment::find($id);
        } catch (QueryException $e) {
            Log::error("Shipment query by Id failed: {$e->getMessage()}", [$e]);
            return null;
        }
    }

    /**
     * @param array $data
     * @return Shipment|null
     */
    public function createShipment(array $data): ?Shipment
    {
        try {
            return Shipment::create($data);
        } catch (QueryException $e) {
            Log::error("Shipment creation query failed: {$e->getMessage()}", [$e]);
            return null;
        }
    }

    /**
     * @param $userId
     * @param array $data
     * @return null
     */
    public function getUserShipments($userId, array $data)
    {
        try {
            $query= Shipment::where('created_by', $userId)->statusFilter();
            return $query->latest()->paginate($data['limit'] ?? Constants::RECORD_LIMIT_PER_PAGE);
        } catch (QueryException $e) {
            Log::error("Shipment retrieval query for user failed: {$e->getMessage()}", [$e]);
            return null;
        }
    }

    /**
     * @param array $data
     * @return null
     */
    public function getShipments(array $data)
    {
        try {
            $query= Shipment::with(['user'])
                ->statusFilter();
            return $query->latest()->paginate($data['limit'] ?? Constants::RECORD_LIMIT_PER_PAGE);

        } catch (QueryException $e) {
            Log::error("Shipment retrieval query failed: {$e->getMessage()}", [$e]);
            return null;
        }
    }

    /**
     * @param UpdateShipmentStatusDTO $dto
     * @param int $shipmentId
     * @return Shipment|null
     */
    public function updateShipmentStatus(UpdateShipmentStatusDTO $dto, int $shipmentId): ?Shipment
    {
        try {
            $shipment = Shipment::find($shipmentId);
            $shipment->status = $dto->status->value;
            $shipment->save();

            return $shipment->fresh();

        } catch (QueryException $e) {
            Log::error("Shipment retrieval query failed: {$e->getMessage()}", [$e]);
            return null;
        }
    }

    /**
     * @param string $trackingNumber
     * @return Shipment|null
     */
    public function findShipmentByTrackingNumber(string $trackingNumber): ?Shipment
    {
        try {
            return Shipment::where('tracking_number', $trackingNumber)->first();
        } catch (QueryException $e) {
            Log::error("Shipment query by tracking number failed: {$e->getMessage()}", [$e]);
            return null;
        }
    }
}
