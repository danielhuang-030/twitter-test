<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * UserRepository
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * construct
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * create
     *
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->create($data);
    }
}
