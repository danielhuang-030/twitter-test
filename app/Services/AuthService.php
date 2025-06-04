<?php

namespace App\Services;

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
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
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_USER_NOT_EXIST,
            ]);
        }

        if (!\Hash::check(data_get($credentials, 'password'), $user->password)) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_UNAUTHORIZED,
            ]);
        }

        // set token
        $tokenResult = $user->createToken(static::TOKEN_KEY);
        $tokenResult->token->save();
        $user->withAccessToken($tokenResult->accessToken);

        // set user
        auth()->setUser($user);

        return $user;
    }
}
