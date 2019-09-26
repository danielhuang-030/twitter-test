<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * find
     *
     * @param int|array $id
     * @return User|Collection
     */
    public function find($id)
    {
        return $this->user->find($id);
    }
}
