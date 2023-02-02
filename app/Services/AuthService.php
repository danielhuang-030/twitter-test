<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    public const TOKEN_KEY = 'user';

    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function attempt(array $credentials): ?User
    {
        $user = $this->userRepository->getByEmail((string) data_get($credentials, 'email'));
        if (empty($user)) {
            return null;
        }

        if (!\Hash::check(data_get($credentials, 'password'), $user->password)) {
            return null;
        }

        // set token
        $tokenResult = $user->createToken(static::TOKEN_KEY);
        $tokenResult->token->save();
        $user->withAccessToken($tokenResult->accessToken);

        // set user
        \Auth::setUser($user);

        return $user;
    }
}
