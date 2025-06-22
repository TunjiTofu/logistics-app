<?php

namespace App\Services\Logging;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Log;

class LoggingService
{
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
}
