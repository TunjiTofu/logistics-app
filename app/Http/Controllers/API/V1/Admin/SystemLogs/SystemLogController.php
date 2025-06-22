<?php

namespace App\Http\Controllers\API\V1\Admin\SystemLogs;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SystemLogsCollection;
use App\Http\Resources\Shipment\ShipmentCollection;
use App\Services\Logging\LoggingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SystemLogController extends Controller
{
    public function __construct(protected LoggingService $loggingService)
    {

    }
    public function getLogs(Request $request): JsonResponse
    {
        try {
            $response = $this->loggingService->getSystemLogs($request->all());

            if (!$response['success']) {
                return $this->errorResponse($response['message']);
            }

            return $this->successResponse($response['message'], new SystemLogsCollection($response['data']));

        } catch (Exception $exception) {
            Log::error('Error retrieving system logs: ', ['exception' => $exception]);
            return $this->internalErrorResponse('An unexpected error occurred while retrieving system logs. Please try again later.');
        }
    }
}
