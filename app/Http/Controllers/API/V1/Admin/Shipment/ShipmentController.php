<?php

namespace App\Http\Controllers\API\V1\Admin\Shipment;

use App\DTOs\Admin\UpdateShipmentStatusDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shipment\UpdateShipmentStatusRequest;
use App\Http\Resources\Shipment\ShipmentCollection;
use App\Services\Shipment\ShipmentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Administrative controller for managing shipments
 *
 * Provides endpoints for administrators to view all shipments and update their statuses.
 * Implements proper error handling and response formatting for administrative operations.
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
     * Retrieves all shipments with optional filtering
     *
     * Administrative endpoint to fetch a paginated list of all shipments in the system.
     * Supports filtering through request parameters.
     *
     * @param Request $request HTTP request containing optional filter parameters
     * @return JsonResponse Paginated collection of shipments or error message
     *
     * @throws Exception When an unexpected error occurs during shipment retrieval
     */
    public function getShipments(Request $request): JsonResponse
    {
        try {
            $response = $this->shipmentService->getShipments($request->all());

            if (!$response['success']) {
                return $this->errorResponse($response['message']);
            }

            return $this->successResponse($response['message'], new ShipmentCollection($response['data']));
        } catch (Exception $exception) {
            Log::error('Error retrieving shipments: ', ['exception' => $exception]);
            return $this->internalErrorResponse('An unexpected error occurred while retrieving shipment records. Please try again later.');
        }
    }

    /**
     * Updates the status of a specific shipment
     *
     * Administrative endpoint to modify the status of an existing shipment.
     * Validates the requested status change and updates the shipment record.
     *
     * @param UpdateShipmentStatusRequest $request Validated request containing the new status
     * @param int $shipmentId ID of the shipment to update
     * @return JsonResponse Success/error response with updated shipment details or error message
     *
     * @throws Exception When an unexpected error occurs during status update
     */
    public function updateStatus(UpdateShipmentStatusRequest $request, int $shipmentId): JsonResponse
    {
        try {
            $dto = UpdateShipmentStatusDTO::fromRequest($request);

            $response = $this->shipmentService->updateShipmentStatus($dto, $shipmentId, $request);

            if (!$response['success']) {
                return $this->errorResponse($response['message']);
            }

            return $this->successResponse($response['message'], $response['data']);
        } catch (Exception $exception) {
            Log::error('Error updating shipment status: ', ['exception' => $exception]);
            return $this->internalErrorResponse('An unexpected error occurred while updating the shipment status. Please try again later.');
        }
    }
}
