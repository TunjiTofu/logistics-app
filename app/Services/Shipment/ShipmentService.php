<?php

namespace App\Services\Shipment;

use App\DTOs\User\CreateShipmentDTO;
use App\Enums\ShipmentStatusEnum;
use App\Http\Resources\Shipment\ShipmentResource;
use App\Jobs\HandleShipmentLogJob;
use App\Models\User;
use App\Repositories\Shipment\ShipmentRepositoryInterface;
use App\Services\Geolocation\GeolocationServiceInterface;
use App\Services\Logging\LoggingService;
use App\Traits\ServiceResponseTrait;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Service class for handling shipment-related operations including creation,
 * tracking number generation, geolocation processing, and asynchronous logging
 */
class ShipmentService
{
    use ServiceResponseTrait;

    /**
     * Constructor for ShipmentService
     *
     * @param ShipmentRepositoryInterface $shipmentRepository Repository for shipment operations
     * @param LoggingService $loggingService Service for handling logging operations
     * @param GeolocationServiceInterface $geolocationService Service for processing address coordinates
     */
    public function __construct(
        protected ShipmentRepositoryInterface $shipmentRepository,
        protected LoggingService $loggingService,
        protected GeolocationServiceInterface $geolocationService
    ) {}

    /**
     * Creates a new shipment record with geolocation data and dispatches an asynchronous logging job
     *
     * @param CreateShipmentDTO $dto Data transfer object containing shipment creation data
     * @param Request $request Current HTTP request instance
     * @return array Response containing status, message, and shipment data with related user
     */
    public function createShipment(CreateShipmentDTO $dto, Request $request): array
    {
        Log::info('creating shipment', [$dto]);

        // Get and validate address coordinates
        $coordinates = $this->validateCoordinates($dto);

        // Prepare shipment data and create record
        $shipmentData = $this->prepareShipmentData($dto, $coordinates);
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
        $shipmentLogData = $this->prepareShipmentLogData('Shipment created', $dto->createdBy->getId(), $request->ip(), $shipmentData);
        HandleShipmentLogJob::dispatch($shipmentLogData)->delay(now()->addSeconds(5));

        return $this->serviceResponse(
            'Shipment created successfully',
            true,
            ShipmentResource::make($shipmentRecord->load('user'))
        );
    }

    /**
     * Validates and retrieves coordinates for origin and destination addresses
     *
     * @param CreateShipmentDTO $dto Data transfer object containing address information
     * @return array|Exception Array containing origin and destination coordinates or exception on failure
     */
    protected function validateCoordinates(CreateShipmentDTO $dto): array|Exception
    {
        // Get address coordinates
        $originAddressCoordinates = $this->geolocationService->getCoordinates($dto->originAddress);
        $destinationAddressCoordinates = $this->geolocationService->getCoordinates($dto->destinationAddress);

        if (!$originAddressCoordinates['latitude'] && !$destinationAddressCoordinates['latitude']) {
            Log::warning('Cannot get Origin Address coordinates at this time');
            return $this->serviceResponse('Cannot get Origin Address coordinates at this time. Please try again later.');
        }

        if (!$destinationAddressCoordinates['latitude'] && !$originAddressCoordinates['latitude']) {
            Log::warning('Cannot get Destination Address coordinates at this time');
            return $this->serviceResponse('Cannot get Destination Address coordinates at this time. Please try again later.');;
        }

        return [
            'origin' => $originAddressCoordinates,
            'destination' => $destinationAddressCoordinates
        ];
    }

    /**
     * Prepares shipment data by combining DTO data with coordinates and system-generated fields
     *
     * @param CreateShipmentDTO $dto Data transfer object containing shipment data
     * @param array $coordinates Array containing origin and destination coordinates
     * @return array Complete shipment data ready for database insertion
     */
    protected function prepareShipmentData(CreateShipmentDTO $dto, array $coordinates): array
    {
        return array_merge(
            [
                ...$dto->toShipmentData(),
                'tracking_number' => $this->generateTrackingNumber(),
                'status' => ShipmentStatusEnum::PENDING->value,
                'origin_latitude' => (float)$coordinates['origin']['latitude'],
                'origin_longitude' => (float)$coordinates['origin']['longitude'],
                'destination_latitude' => (float)$coordinates['destination']['latitude'],
                'destination_longitude' => (float)$coordinates['destination']['longitude'],
            ]
        );
    }

    /**
     * Prepares data for shipment logging
     *
     * @param string $action The action being logged
     * @param int $createdBy User ID who performed the action
     * @param string $ipAddress IP address of the request
     * @param array $data Additional metadata to be logged
     * @return array Prepared log data
     */
    protected function prepareShipmentLogData(string $action, int $createdBy, string $ipAddress, array $data): array
    {
        return [
            'action' => $action,
            'user_id' => $createdBy,
            'ip_address' => $ipAddress,
            'metadata' => $data,
        ];
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

    public function getUserShipments(User $user, array $data): array
    {
        Log::info('Getting shipment records for user: ', ['user' => $user->email]);;
        $result = $this->shipmentRepository->getUserShipments($user->getId(), $data);
        if (empty($result)) {
            Log::warning('No Shipment record found for user: ' . $user->getId());;
            return $this->serviceResponse('No Shipment record available at the moment');
        }

        return $this->serviceResponse('User Shipment record', true, $result);
    }
}
