<?php

namespace App\Services;

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function create(array $data): ?User
    {
        $userData = [
            'name' => (string) data_get($data, 'name'),
            'email' => (string) data_get($data, 'email'),
            'password' => \Hash::make((string) data_get($data, 'password')),
        ];

        $user = $this->userRepository->create($userData);
        if (empty($user)) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_USER_ADD,
            ]);

            return null;
        }

        return $user;
    }

    public function getUser(int $id): ?User
    {
        $user = $this->userRepository->getById($id);
        if (empty($user)) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_USER_NOT_EXIST,
            ]);

            return null;
        }

        return $user;
    }
}
