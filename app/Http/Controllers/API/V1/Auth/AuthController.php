<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\DTOs\User\CreateUserDTO;
use App\DTOs\User\UserLoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserSignupRequest;
use App\Services\Auth\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(protected AuthenticationService $authenticationService)
    {
    }

    public function register(UserSignupRequest $request): JsonResponse
    {
        try {
            $dto = CreateUserDTO::fromRequest($request);
            $response = $this->authenticationService->createUser($dto);

            if (!$response['success']) {
                return $this->errorResponse($response['message']);
            }

            return $this->successResponse($response['message'], $response['data']);
        } catch (\Exception $exception) {
            Log::error('Error registering user: ', ['exception' => $exception]);
            return $this->internalErrorResponse('An unexpected error occurred while creating the user record. Please try again later.');
        }
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        try {
            $dto = UserLoginDTO::fromRequest($request);
            $response = $this->authenticationService->login($dto);

            if (!$response['success']) {
                return $this->errorResponse($response['message']);
            }

            return $this->successResponse($response['message'], $response['data']);
        } catch (\Exception $exception) {
            Log::error('Error registering user: ', ['exception' => $exception]);
            return $this->internalErrorResponse('An unexpected error occurred while creating the user record. Please try again later.');
        }
    }
}
