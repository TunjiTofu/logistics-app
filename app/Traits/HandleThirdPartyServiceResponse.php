<?php

namespace App\Traits;

use App\Enums\StatusEnum;
use Symfony\Component\HttpFoundation\Response;

trait HandleThirdPartyServiceResponse
{
    protected function formatServiceResponse(bool $success, string $status,  string $message, mixed $data = null, ?array $metadata = null, int $statusCode = Response::HTTP_OK): array
    {
        return [
            'success' => $success,
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'metadata' => $metadata,
            'statusCode' => $statusCode,
        ];
    }

    protected function serviceProviderSuccessResponse(
        StatusEnum $status,
        string $message,
        mixed $data = null,
        ?array $metadata = null,
        int $statusCode = Response::HTTP_OK
    ): array
    {
        return $this->formatServiceResponse(true, $status->value, $message, $data, $metadata, $statusCode);
    }

    protected function serviceProviderPendingResponse(
        StatusEnum $status,
        string $message,
        mixed $data = null,
        ?array $metadata = null,
        int $statusCode = Response::HTTP_ACCEPTED
    ): array
    {
        return $this->formatServiceResponse(false, $status->value, $message, $data, $metadata, $statusCode);
    }

    protected function serviceProviderErrorResponse(
        StatusEnum $status,
        string $message,
        mixed $errors = null,
        ?array $metadata = null,
        int $statusCode = Response::HTTP_BAD_REQUEST
    ): array
    {
        return $this->formatServiceResponse(false, $status->value, $message, $errors, $metadata, $statusCode);
    }
}
