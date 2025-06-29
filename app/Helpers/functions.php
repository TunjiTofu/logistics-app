<?php

use App\Enums\UserTypeEnum;
use App\Models\User;

if (!function_exists('generateAuthToken')) {
    function generateAuthToken(User $user, string $identifier, ): string
    {
        $user->tokens()->delete();

        $ability = $user->role === UserTypeEnum::USER->value ? 'user-access' : 'admin-access';

        $token = $user->createToken($identifier, [$ability]);

        if ($ability === 'user-access') {
            $token->accessToken->update([
                'expires_at' => now()->addMinutes(60)
            ]);
        }

        return $token->plainTextToken;
    }
}

if (!function_exists('prepareShipmentLogData')) {
    function prepareShipmentLogData(string $action, int $userId, string $ipAddress, array $data): array
    {
        return [
            'action' => $action,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'metadata' => $data,
        ];
    }

}

