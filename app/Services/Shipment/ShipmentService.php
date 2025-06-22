<?php

namespace App\Services\Shipment;

use App\DTOs\User\CreateShipmentDTO;
use App\Enums\ShipmentStatusEnum;
use App\Http\Resources\Shipment\ShipmentResource;
use App\Repositories\Shipment\ShipmentRepositoryInterface;
use App\Services\Logging\LoggingService;
use App\Traits\ServiceResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Service class for handling shipment-related operations
 */
class ShipmentService
{
    use ServiceResponseTrait;

    /**
     * Constructor for ShipmentService
     *
     * @param ShipmentRepositoryInterface $shipmentRepository Repository for shipment operations
     * @param LoggingService $loggingService Service for handling logging operations
     */
    public function __construct(
        protected ShipmentRepositoryInterface $shipmentRepository,
        protected LoggingService $loggingService,
    ) {}

    /**
     * Creates a new shipment record
     *
     * @param CreateShipmentDTO $dto Data transfer object containing shipment creation data
     * @param Request $request Current HTTP request instance
     * @return array Response containing status, message, and shipment data
     */
    public function createShipment(CreateShipmentDTO $dto, Request $request): array
    {
        Log::info('creating shipment', [$dto]);

        // Prepare shipment data and create record
        $shipmentData = $this->prepareShipmentData($dto);
        $shipmentRecord = $this->shipmentRepository->createShipment($shipmentData);

        if (!$shipmentRecord) {
            Log::warning('Shipment record not created');
            return $this->serviceResponse('Shipment record not created');
        }

        Log::info('Shipment record created', [
            'id' => $shipmentRecord->id,
            'tracking_number' => $shipmentRecord->tracking_number
        ]);

        // Log the action
        $this->loggingService->log('Shipment created', $dto->createdBy->getId(), $request->ip(), $shipmentData);

        return $this->serviceResponse(
            'Shipment created successfully',
            true,
            ShipmentResource::make($shipmentRecord->load('user'))
        );
    }

    /**
     * Prepares shipment data for creation by merging DTO data with additional required fields
     *
     * @param CreateShipmentDTO $dto Data transfer object containing initial shipment data
     * @return array Prepared shipment data
     */
    protected function prepareShipmentData(CreateShipmentDTO $dto): array
    {
        return array_merge(
            $dto->toShipmentData(),
            [
                'tracking_number' => $this->generateTrackingNumber(),
                'status' => ShipmentStatusEnum::PENDING->value,
            ]
        );
    }

    /**
     * Generates a unique tracking number for the shipment
     *
     * @return string UUID string to be used as a tracking number
     */
    public function generateTrackingNumber(): string
    {
        return Str::uuid()->toString();
    }
}
