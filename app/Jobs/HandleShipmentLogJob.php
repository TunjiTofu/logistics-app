<?php

namespace App\Jobs;

use App\Services\Logging\LoggingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class HandleShipmentLogJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $data)
    {
        Log::info('HandleShipmentLogJob', $this->data);;
    }

    /**
     * Execute the job.
     */
    public function handle(LoggingService $loggingService): void
    {
        try {
            $loggingService->log(
                $this->data['action'],
                $this->data['user_id'],
                $this->data['ip_address'],
                $this->data['metadata']
            );
        } catch (\Throwable $e) {
            Log::error('Error logging shipment', [
                'data' => $this->data,
                'exception' => $e
            ]);
        }
    }
}
