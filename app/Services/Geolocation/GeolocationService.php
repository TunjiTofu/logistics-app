<?php

namespace App\Services\Geolocation;

use App\Enums\StatusEnum;
use App\Traits\HandleThirdPartyServiceResponse;
use App\Utility\Constants;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Service for handling geocoding operations using the OpenCage API
 * Provides functionality to convert addresses into geographical coordinates
 */
class GeolocationService implements GeolocationServiceInterface
{
    use HandleThirdPartyServiceResponse;

    private string $apiKey;
    private string $baseUrl;

    public const SERVICE_PROVIDER = 'OpenCage';
    protected const END_POINTS = [
        'get_coordinates' => 'geocode/v1/json',
    ];

    /**
     * Initialize the geolocation service with configuration from the services config
     */
    public function __construct()
    {
        $this->apiKey = config('services.opencage.api_key');
        $this->baseUrl = rtrim(config('services.opencage.base_url'), '/') . '/';

        Log::info('Geolocation service initialized', ['base_url' => $this->baseUrl]);
    }

    /**
     * Retrieves geographical coordinates for a given address
     *
     * @param string $address The address to geocode
     * @return array|null Array containing latitude and longitude or null on failure
     */
    public function getCoordinates(string $address): ?array
    {
        try {
            $response = $this->sendApiRequest(
                self::END_POINTS['get_coordinates'],
                $this->buildRequestPayload($address)
            );

            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Builds the request payload for the geocoding API
     *
     * @param string $address Address to be geocoded
     * @return array Request parameters including API key and query
     */
    protected function buildRequestPayload(string $address): array
    {
        return [
            'q' => $address,
            'key' => $this->apiKey,
            'limit' => 1,
        ];
    }

    /**
     * Sends HTTP request to the geocoding API with retry and timeout handling
     *
     * @param string $endpoint API endpoint to call
     * @param array|null $payload Request parameters
     * @return array API response or error message
     */
    protected function sendApiRequest(string $endpoint, array $payload = null): array
    {
        try {
            $response = Http::timeout(Constants::API_REQUEST_TIMEOUT)
                ->retry(2, 100)
                ->get($this->baseUrl . $endpoint, $payload)
                ->throw()
                ->json();
            Log::debug(self::SERVICE_PROVIDER.' API Response', [
                'provider' => self::SERVICE_PROVIDER,
                'endpoint' => $endpoint,
                'response' => $response
            ]);
            return $response;
        } catch (ConnectException $e) {
            Log::error('API Connection failed', [
                'provider' => self::SERVICE_PROVIDER,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'message' => 'Connection timeout. Please try again later.'
            ];
        } catch (Throwable $e) {
            Log::error('API Request Failed', [
                'provider' => self::SERVICE_PROVIDER,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'message' => 'Service temporarily unavailable',
            ];
        }
    }

    /**
     * Processes the API response and extracts coordinates or handles errors
     *
     * @param array $response Raw API response
     * @return array Processed coordinates or null values on failure
     */
    protected function handleApiResponse(array $response): array
    {
        if (!$this->isValidResponse($response)) {
            Log::alert('Invalid response', [
                'status' => StatusEnum::FAILED,
                'response' => "No response from ". self::SERVICE_PROVIDER,
                'provider' => self::SERVICE_PROVIDER
            ]);
            return [
                'latitude' => null,
                'longitude' => null,
            ];
        }

        if ($this->isFailedResponse($response)) {
            return $this->failedResponse($response);
        }

        return $this->successResponse($response);
    }

    /**
     * Checks if the API response contains valid coordinate data
     *
     * @param array $response API response to validate
     * @return bool True if response contains valid coordinates
     */
    protected function isValidResponse(array $response): bool
    {
        return isset($response['results'][0]['geometry']['lat'])
            && isset($response['results'][0]['geometry']['lng'])
            && isset($response['status']['code'])
            && is_numeric($response['results'][0]['geometry']['lat'])
            && is_numeric($response['results'][0]['geometry']['lng']);
    }

    /**
     * Checks if the API response indicates a failed request
     *
     * @param array $response API response to check
     * @return bool True if response indicates failure
     */
    protected function isFailedResponse(array $response): bool
    {
        return isset($response['status']['code']) && $response['status']['code'] >= 400;
    }

    /**
     * Handles failed API responses
     *
     * @return array Null coordinates with logging
     */
    protected function failedResponse(): array
    {
        Log::debug('Failed getting geolocation', [
            'status' => StatusEnum::FAILED,
            'response' => $response['message'] ?? 'Failed getting geolocation',
            'provider' => self::SERVICE_PROVIDER
        ]);
        return [
            'latitude' => null,
            'longitude' => null,
        ];
    }

    /**
     * Extracts coordinates from a successful API response
     *
     * @param array $response Successful API response
     * @return array Extracted latitude and longitude
     */
    protected function successResponse(array $response): array
    {
        $geometry = $response['results'][0]['geometry'];
        return [
            'latitude' => $geometry['lat'],
            'longitude' => $geometry['lng'],
        ];
    }
}
