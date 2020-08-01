<?php

namespace App\Services;

use App\Models\UserFollow;
use App\Repositories\UserFollowRepository;

class FollowService
{
    /**
     * UserFollowRepository.
     *
     * @var UserFollowRepository
     */
    protected $userFollowRepository;

    /**
     * construct.
     *
     * @param UserFollowRepository $userFollowRepository
     */
    public function __construct(UserFollowRepository $userFollowRepository)
    {
        $this->userFollowRepository = $userFollowRepository;
    }

    /**
     * add.
     *
     * @param int $followId
     * @param int $userId
     *
     * @return UserFollow
     */
    public function add(int $followId, int $userId)
    {
        return $this->userFollowRepository->add($followId, $userId);
    }

    /**
     * del.
     *
     * @param int $followId
     * @param int $userId
     *
     * @return bool
     */
    public function del(int $followId, int $userId)
    {
        if (0 === $this->userFollowRepository->del($followId, $userId)) {
            return false;
        }

        return true;
    }
}
