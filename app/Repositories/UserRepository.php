<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * User.
     *
     * @var User
     */
    private $model;

    /**
     * construct.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * create.
     *
     * @param array $data
     *
     * @return User
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * find.
     *
     * @param int|array $id
     *
     * @return User|Collection
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * get by email.
     *
     * @param string $email
     *
     * @return User
     */
    public function getByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }
}
