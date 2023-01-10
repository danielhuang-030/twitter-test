<?php

namespace App\Services;

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

        return $this->userRepository->create($userData);
    }

    public function getUser(int $id): ?User
    {
        return $this->userRepository->find($id);
    }
}
