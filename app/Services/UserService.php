<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * token key.
     *
     * @var string
     */
    const TOKEN_KEY = 'user';

    /**
     * UserRepository.
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * construct.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->create($data);
    }

    /**
     * attempt.
     *
     * @param array $credentials
     *
     * @return User
     */
    public function attempt(array $credentials)
    {
        $user = $this->userRepository->getByEmail($credentials['email']);
        if (null === $user) {
            return null;
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        // set token
        $tokenResult = $user->createToken(static::TOKEN_KEY);
        $tokenResult->token->save();
        $user->withAccessToken($tokenResult->accessToken);

        // set user
        Auth::setUser($user);

        return $user;
    }

    /**
     * get user.
     *
     * @param int $id
     *
     * @return User
     */
    public function getUser(int $id)
    {
        return $this->userRepository->find($id);
    }
}
