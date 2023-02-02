<?php

namespace App\Services;

use App\Repositories\UserRepository;

class FollowService
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function follow(int $followId, int $userId): bool
    {
        $user = $this->userRepository->getById($userId);
        if (empty($user)) {
            return false;
        }
        $user->following()->syncWithoutDetaching((array) $followId);

        return true;
    }

    public function unfollow(int $followId, int $userId): bool
    {
        $user = $this->userRepository->getById($userId);
        if (empty($user)) {
            return false;
        }
        if (0 === $user->following()->detach((array) $followId)) {
            return false;
        }

        return true;
    }
}
