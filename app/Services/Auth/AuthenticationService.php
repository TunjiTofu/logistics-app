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

class AuthenticationService
{
    use ServiceResponseTrait;

    public function __construct(protected User $userModel, protected UserRepository $userRepository)
    {

    }

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

    public function login(UserLoginDTO $dto, Request $request): array
    {
        Log::info('login in user', ['email' => $dto->email]);

        $user = $this->userRepository->getUserByEmail($dto->email);

        if (!$user) {
            Log::warning('user record not found');;
            return $this->serviceResponse('User record not found');;
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

    public function logout(Authenticatable $user): array
    {
        Log::info('Logging out user', ['user' => $user->email]);
        $user->tokens()->delete();
        return $this->serviceResponse('User successfully logged out', true);
    }

}
