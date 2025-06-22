<?php

namespace App\Services\Logging;

use App\Models\SystemLog;
use App\Repositories\Shipment\ShipmentRepositoryInterface;
use App\Repositories\SystemLogs\SystemLogRepository;
use App\Services\Geolocation\GeolocationServiceInterface;
use App\Traits\ServiceResponseTrait;
use Illuminate\Support\Facades\Log;

class LoggingService
{
    use ServiceResponseTrait;

    public function __construct(protected SystemLogRepository $systemLogRepository)
    {}


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

    public function getSystemLogs(array $data): array
    {
        Log::info('Getting system logs by Admin');
        $result = $this->systemLogRepository->getLogs($data);

        if (empty($result)) {
            Log::warning('No System Logs found');;
            return $this->serviceResponse('No System logs available at the moment');
        }

        return $this->serviceResponse('System Logs records', true, $result);
    }
}
