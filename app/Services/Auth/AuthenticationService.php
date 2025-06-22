<?php

namespace App\Services\Auth;

use App\DTOs\User\CreateUserDTO;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Repositories\User\UserRepository;
use App\Traits\ServiceResponseTrait;
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
            Log::info('email already exists');
            return $this->serviceResponse('This email already exists');
        }

        $user = $this->userRepository->createUser($dto);
        if (!$user) {
            Log::info('user not created');
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


}
