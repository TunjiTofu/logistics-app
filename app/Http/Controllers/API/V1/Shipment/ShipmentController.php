<?php

namespace App\Http\Controllers\API\V1\Shipment;

use App\DTOs\User\CreateShipmentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Shipment\CreateShipmentRequest;
use App\Services\Shipment\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShipmentController extends Controller
{
    public function __construct(protected ShipmentService $shipmentService)
    {}
    public function createShipment(CreateShipmentRequest $request)
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
}
