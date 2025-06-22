<?php

namespace App\Repositories\User;

use App\DTOs\Auth\CreateUserDTO;
use App\Models\User;

class UserRepository
{
    public function __construct(protected User $userModel)
    {}

    public function createUser(CreateUserDTO $dto): ?User
    {
        $data = $dto->toUserData();
        return User::create($data);
    }

    public function emailExists(string $email)
    {
        return $this->userModel->where('email', $email)->exists();
    }

    public function getUserByEmail(string $email)
    {
        return $this->userModel->where('email', $email)->first();
    }
}
