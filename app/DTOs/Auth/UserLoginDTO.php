<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserSignupRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UserLoginDTO extends BaseDTO
{
    public string $email;
    public string $password;

    /**
     * @throws UnknownProperties
     */
    public static function fromRequest(UserLoginRequest $request): self
    {
        return static::createFromRequest($request->validated());
    }
}
