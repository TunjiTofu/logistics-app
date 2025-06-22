<?php

namespace App\Http\Controllers\API\V1\Admin\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shipment\ShipmentCollection;
use App\Services\Shipment\ShipmentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShipmentController extends Controller
{
    public function __construct(protected ShipmentService $shipmentService)
    {}

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
}
