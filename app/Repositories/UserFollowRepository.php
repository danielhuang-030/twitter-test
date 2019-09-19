<?php

namespace App\Repositories;

use App\Models\UserFollow;

class UserFollowRepository
{
    /**
     * UserFollow
     *
     * @var UserFollow
     */
    private $userFollow;

    /**
     * construct
     *
     * @param UserFollow $userFollow
     */
    public function __construct(UserFollow $userFollow)
    {
        $this->userFollow = $userFollow;
    }

    /**
     * add
     *
     * @param integer $followId
     * @param integer $userId
     * @return UserFollow
     */
    public function add(int $followId, int $userId)
    {
        return $this->userFollow->firstOrCreate([
            'user_id'   => $userId,
            'follow_id' => $followId,
        ]);
    }

    /**
     * del
     *
     * @param integer $followId
     * @param integer $userId
     * @return int
     */
    public function del(int $followId, int $userId)
    {
        return $this->userFollow
            ->where('user_id', $userId)
            ->where('follow_id', $followId)
            ->delete();
    }
}
