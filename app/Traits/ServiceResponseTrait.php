<?php

namespace App\Traits;

trait ServiceResponseTrait
{
    protected function serviceResponse(string $message, bool $success = false, $data = null): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];
    }
}
