<?php

namespace App\Http\Controllers\API\V1\Admin\SystemLogs;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SystemLogsCollection;
use App\Services\Logging\LoggingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Administrative controller for managing system logs
 *
 * Provides endpoints for administrators to access and view system activity logs.
 * Implements proper error handling and response formatting for log retrieval operations.
 */
class SystemLogController extends Controller
{
    /**
     * Constructor for SystemLogController
     *
     * @param LoggingService $loggingService Service handling system logging operations
     */
    public function __construct(protected LoggingService $loggingService)
    {
    }

    /**
     * Retrieves system logs with optional filtering
     *
     * Administrative endpoint to fetch a paginated list of system activity logs.
     * Supports filtering through request parameters for targeted log retrieval.
     *
     * @param Request $request HTTP request containing optional filter parameters
     * @return JsonResponse Paginated collection of system logs or error message
     *
     * @throws Exception When an unexpected error occurs during log retrieval
     */
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
