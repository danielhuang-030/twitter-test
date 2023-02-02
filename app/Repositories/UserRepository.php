<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function getByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    protected function model(): string
    {
        return User::class;
    }
}
