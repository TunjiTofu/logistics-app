<?php

namespace App\Repositories\Shipment;

use App\DTOs\Admin\UpdateShipmentStatusDTO;
use App\Models\Shipment;

interface ShipmentRepositoryInterface
{
    public function createShipment(array $data);
    public function findShipmentById(int $id): ?Shipment;

    public function getUserShipments($userId, array $data);

    public function getShipments(array $data);

    public function updateShipmentStatus(UpdateShipmentStatusDTO $dto, int $shipmentId): ?Shipment;

    public function findShipmentByTrackingNumber(string $trackingNumber): ?Shipment;
}
