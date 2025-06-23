<?php

namespace App\Repositories\User;

use App\DTOs\Auth\CreateUserDTO;
use App\Models\User;

class UserRepository
{
    public function __construct(protected User $userModel)
    {}

    /**
     * @param CreateUserDTO $dto
     * @return User|null
     */
    public function createUser(CreateUserDTO $dto): ?User
    {
        $data = $dto->toUserData();
        return User::create($data);
    }

    /**
     * @param string $email
     * @return mixed
     */
    public function emailExists(string $email)
    {
        return $this->userModel->where('email', $email)->exists();
    }

    /**
     * @param string $email
     * @return mixed
     */
    public function getUserByEmail(string $email)
    {
        return $this->userModel->where('email', $email)->first();
    }
}
