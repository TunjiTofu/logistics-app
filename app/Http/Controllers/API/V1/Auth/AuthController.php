<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\DTOs\Auth\CreateUserDTO;
use App\DTOs\Auth\UserLoginDTO;
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

    /**
     * @param UserSignupRequest $request
     * @return JsonResponse
     */
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

    /**
     * @param UserLoginRequest $request
     * @return JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        try {
            $dto = UserLoginDTO::fromRequest($request);
            $response = $this->authenticationService->login($dto, $request);

            if (!$response['success']) {
                return $this->errorResponse($response['message']);
            }

            return $this->successResponse($response['message'], $response['data']);
        } catch (\Exception $exception) {
            Log::error('Error registering user: ', ['exception' => $exception]);
            return $this->internalErrorResponse('An unexpected error occurred while logging in the user record. Please try again later.');
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $response = $this->authenticationService->logout($request->user());

            if (!$response['success']) {
                return $this->errorResponse($response['message']);
            }

            return $this->successResponse($response['message'], $response['data']);
        } catch (\Exception $exception) {
            Log::error('Error registering user: ', ['exception' => $exception]);
            return $this->internalErrorResponse('An unexpected error occurred while logging out the user. Please try again later.');
        }
    }
}
