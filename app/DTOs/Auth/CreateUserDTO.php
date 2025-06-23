<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;
use App\Http\Requests\Auth\UserSignupRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CreateUserDTO extends BaseDTO
{
    public string $name;
    public string $email;
    public string $role;
    public string $password;

    /**
     * @param UserSignupRequest $request
     * @return self
     * @throws UnknownProperties
     */
    public static function fromRequest(UserSignupRequest $request): self
    {
        return static::createFromRequest($request->validated());
    }

    /**
     * @return array
     */
    public function toUserData(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'password' => $this->password,
        ];
    }
}
