<?php

namespace App\Http\Controllers\API\V1\Shipment;

use App\DTOs\User\CreateShipmentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Shipment\CreateShipmentRequest;
use App\Http\Resources\Shipment\ShipmentCollection;
use App\Services\Shipment\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller handling shipment-related HTTP requests
 *
 * Manages the creation and retrieval of shipments through the API endpoints.
 * Implements proper error handling and response formatting for the shipment operations.
 */
class ShipmentController extends Controller
{
    /**
     * Constructor for ShipmentController
     *
     * @param ShipmentService $shipmentService Service handling shipment business logic
     */
    public function __construct(protected ShipmentService $shipmentService)
    {}

    /**
     * Creates a new shipment based on the provided request data
     *
     * Validates the incoming request, transforms it into a DTO, and processes
     * the shipment creation through the shipment service.
     *
     * @param CreateShipmentRequest $request Validated request containing shipment creation data
     * @return JsonResponse Success/error response with shipment details or error message
     *
     * @throws \Exception When an unexpected error occurs during shipment creation
     */
    public function createShipment(CreateShipmentRequest $request): JsonResponse
    {
        try {
            $dto = CreateShipmentDTO::fromRequest($request);
            $response = $this->shipmentService->createShipment($dto, $request);

            if (!$response['success']) {
                return $this->errorResponse($response['message']);
            }

            return $this->successResponse($response['message'], $response['data']);
        } catch (\Exception $exception) {
            Log::error('Error registering user: ', ['exception' => $exception]);
            return $this->internalErrorResponse('An unexpected error occurred while creating shipment record. Please try again later.');
        }
    }

    /**
     * Retrieves shipments for the authenticated user
     *
     * Fetches and returns a paginated list of shipments associated with the current user.
     * Supports filtering through request parameters.
     *
     * @param Request $request HTTP request containing optional filter parameters
     * @return JsonResponse Paginated collection of shipments or error message
     *
     * @throws \Exception When an unexpected error occurs during shipment retrieval
     */
    public function getShipments(Request $request): JsonResponse
    {
        try {
            $response = $this->shipmentService->getUserShipments($request->user(), $request->all());

            if (!$response['success']) {
                return $this->errorResponse($response['message']);
            }

            return $this->successResponse($response['message'], new ShipmentCollection($response['data']));
        } catch (\Exception $exception) {
            Log::error('Error retrieving shipments: ', ['exception' => $exception]);
            return $this->internalErrorResponse('An unexpected error occurred while retrieving shipment records. Please try again later.');
        }
    }
}
