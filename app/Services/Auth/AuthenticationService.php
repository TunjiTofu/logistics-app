<?php

namespace App\Services\Auth;

use App\DTOs\Auth\CreateUserDTO;
use App\DTOs\Auth\UserLoginDTO;
use App\Http\Resources\User\UserResource;
use App\Jobs\HandleSystemLoggingJob;
use App\Models\User;
use App\Repositories\User\UserRepository;
use App\Traits\ServiceResponseTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling user authentication operations
 *
 * Manages user registration, login, and logout processes.
 * Handles token generation and user session management.
 */
class AuthenticationService
{
    use ServiceResponseTrait;

    /**
     * Constructor for AuthenticationService
     *
     * @param User $userModel User model instance for user operations
     * @param UserRepository $userRepository Repository for user data operations
     */
    public function __construct(protected User $userModel, protected UserRepository $userRepository)
    {
    }

    /**
     * Creates a new user account
     *
     * Handles user registration process including email uniqueness validation
     * and authentication token generation.
     *
     * @param CreateUserDTO $dto Data transfer object containing user registration data
     * @return array Response containing success status, message, and user data with token
     *               Format: ['success' => bool, 'message' => string, 'data' => array{token: string, user: UserResource}]
     */
    public function createUser(CreateUserDTO $dto): array
    {
        Log::info('creating user', ['email' => $dto->email]);

        if ($this->userRepository->emailExists($dto->email)) {
            Log::warning('email already exists');
            return $this->serviceResponse('This email already exists');
        }

        $user = $this->userRepository->createUser($dto);
        if (!$user) {
            Log::warning('user not created');
            return $this->serviceResponse('User not created');
        }

        Log::info('user created');
        $user->markAsLoggedIn();

        $data = [
            'token' => generateAuthToken($user, $dto->email),
            'user' => UserResource::make($user),
        ];

        return $this->serviceResponse('User created successfully', true, $data);
    }

    /**
     * Authenticates a user and creates a session
     *
     * Validates user credentials, generates authentication token,
     * and logs the login activity.
     *
     * @param UserLoginDTO $dto Data transfer object containing login credentials
     * @param Request $request HTTP request instance for IP address logging
     * @return array Response containing success status, message, and authentication data
     *               Format: ['success' => bool, 'message' => string, 'data' => array{token: string, user: UserResource}]
     */
    public function login(UserLoginDTO $dto, Request $request): array
    {
        Log::info('login in user', ['email' => $dto->email]);

        $user = $this->userRepository->getUserByEmail($dto->email);

        if (!$user) {
            Log::warning('user record not found');
            return $this->serviceResponse('User record not found');
        }

        if (! $user->checkPassword($dto->password)) {
            Log::warning('Incorrect Password Supplied');
            return $this->serviceResponse('Incorrect Password');
        }

        Log::info('user login successful');
        $user->markAsLoggedIn();

        // Log the action
        $userLoginData = prepareShipmentLogData('User login', $user->getId(), $request->ip(), $dto->toLoginData());
        HandleSystemLoggingJob::dispatch($userLoginData)->delay(now()->addSeconds(2));

        $data = [
            'token' => generateAuthToken($user, $dto->email),
            'user' => UserResource::make($user),
        ];

        return $this->serviceResponse('User login successful', true, $data);
    }

    /**
     * Logs out a user from the system
     *
     * Invalidates all access tokens for the user and ends their session.
     *
     * @param Authenticatable $user The authenticated user to log out
     * @return array Response indicating logout success
     *               Format: ['success' => bool, 'message' => string]
     */
    public function logout(Authenticatable $user): array
    {
        Log::info('Logging out user', ['user' => $user->email]);
        $user->tokens()->delete();
        return $this->serviceResponse('User successfully logged out', true);
    }
}
