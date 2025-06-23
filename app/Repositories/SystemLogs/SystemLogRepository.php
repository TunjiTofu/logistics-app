<?php

namespace App\Repositories\SystemLogs;

use App\Models\Shipment;
use App\Models\SystemLog;
use App\Utility\Constants;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SystemLogRepository
{

    public function getLogs(array $data)
    {
        try {
            $query= SystemLog::with(['user']);
            return $query->latest()->paginate($data['limit'] ?? Constants::RECORD_LIMIT_PER_PAGE);

        } catch (QueryException $e) {
            Log::error("Shipment retrieval query failed: {$e->getMessage()}", [$e]);
            return null;
        }
    }
}
