<?php

namespace App\Services;

use App\Models\User;
use App\Params\PostParam;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
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
     * PostRepository.
     *
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * construct.
     *
     * @param UserRepository $userRepository
     * @param PostRepository $postRepository
     */
    public function __construct(UserRepository $userRepository, PostRepository $postRepository)
    {
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
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

    /**
     * get posts.
     *
     * @param PostParam $param
     *
     * @return LengthAwarePaginator
     */
    public function getPosts(PostParam $param)
    {
        return $this->postRepository->getByParam($param);
    }
}
