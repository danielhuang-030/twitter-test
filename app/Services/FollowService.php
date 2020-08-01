<?php

namespace App\Services;

use App\Repositories\UserRepository;

class FollowService
{
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
     * follow.
     *
     * @param int $followId
     * @param int $userId
     *
     * @return bool
     */
    public function follow(int $followId, int $userId)
    {
        $user = $this->userRepository->find($userId);
        if (null === $user) {
            return false;
        }
        $user->following()->syncWithoutDetaching((array) $followId);

        return true;
    }

    /**
     * unfollow.
     *
     * @param int $followId
     * @param int $userId
     *
     * @return bool
     */
    public function unfollow(int $followId, int $userId)
    {
        $user = $this->userRepository->find($userId);
        if (null === $user) {
            return false;
        }
        if (0 === $user->following()->detach((array) $followId)) {
            return false;
        }

        return true;
    }
}
