<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * User
     *
     * @var User
     */
    private $user;

    /**
     * construct
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * create
     *
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        return $this->user->create($data);
    }
}
