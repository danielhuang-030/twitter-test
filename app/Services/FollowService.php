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
        $user = $this->userRepository->getById($userId, [
            'following',
        ]);
        if (empty($user)) {
            return false;
        }
        if ($user->id == $followId) {
            return false;
        }
        if ($user->following->pluck('id')->contains($followId)) {
            return false;
        }
        $user->following()->syncWithoutDetaching((array) $followId);

        return true;
    }

    public function unfollow(int $followId, int $userId): bool
    {
        $user = $this->userRepository->getById($userId, [
            'following',
        ]);
        if (empty($user)) {
            return false;
        }
        if ($user->id == $followId) {
            return false;
        }
        if (!$user->following->pluck('id')->contains($followId)) {
            return false;
        }
        $user->following()->detach((array) $followId);

        return true;
    }
}
