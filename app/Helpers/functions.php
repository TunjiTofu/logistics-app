<?php

use App\Enums\UserType;
use App\Models\User;

if (!function_exists('generateAuthToken')) {
    function generateAuthToken(User $user, string $identifier, ): string
    {
        $ability = $user->role === UserType::USER ? 'user-access' : 'admin-access';

        $token = $user->createToken($identifier, [$ability]);

        if ($ability === 'admin-access') {
            $token->accessToken->update([
                'expires_at' => now()->addMinutes(30)
            ]);
        }

        return $token->plainTextToken;
    }
}
