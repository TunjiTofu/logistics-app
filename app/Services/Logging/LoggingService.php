<?php

namespace App\Services\Logging;

use App\Models\SystemLog;
use App\Repositories\SystemLogs\SystemLogRepository;
use App\Traits\ServiceResponseTrait;
use Illuminate\Support\Facades\Log;

/**
 * Service for managing system-wide logging operations
 *
 * Handles the creation and retrieval of system activity logs,
 * providing a centralized way to track user actions and system events.
 */
class LoggingService
{
    use ServiceResponseTrait;

    /**
     * Constructor for LoggingService
     *
     * @param SystemLogRepository $systemLogRepository Repository for system log operations
     */
    public function __construct(protected SystemLogRepository $systemLogRepository)
    {}

    /**
     * Records a new system activity log
     *
     * Creates a new log entry with user action details, IP address, and optional metadata.
     * Each log entry is stored in the database and also logged to the application's logging system.
     *
     * @param string $action Description of the action being logged
     * @param int $userId ID of the user performing the action
     * @param string $ipAddress IP address from which the action was performed
     * @param array|null $metaData Optional additional data to be stored with the log
     * @return void
     *
     * @throws \Illuminate\Database\QueryException When database operation fails
     */
    public function log(string $action, int $userId, string $ipAddress, ?array $metaData = null): void
    {
        Log::info('Logging action: ' . $action . ' by user: ' . $userId);
        SystemLog::create([
            'action' => $action,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'metadata' => $metaData,
        ]);
    }

    /**
     * Retrieves system logs with optional filtering
     *
     * Fetches paginated system logs based on provided criteria.
     * Returns a formatted response containing the logs or an error message.
     *
     * @param array $data Filter parameters for log retrieval
     * @return array Response containing success status, message, and log data
     *               Format: ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function getSystemLogs(array $data): array
    {
        Log::info('Getting system logs by Admin');
        $result = $this->systemLogRepository->getLogs($data);

        if (empty($result)) {
            Log::warning('No System Logs found');
            return $this->serviceResponse('No System logs available at the moment');
        }

        return $this->serviceResponse('System Logs records', true, $result);
    }
}
