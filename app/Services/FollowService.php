<?php

namespace App\Services;

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Repositories\UserRepository;

class FollowService
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function follow(int $followId, int $userId): bool
    {
        $follower = $this->userRepository->getById($followId);
        if (empty($follower)) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_USER_NOT_EXIST,
            ]);
        }

        $user = $this->userRepository->getById($userId, [
            'following',
        ]);
        if (empty($user)) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_USER_NOT_EXIST,
            ]);
        }

        if ($user->id == $followId) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_FOLLOW_SELF,
            ]);
        }

        if ($user->following->pluck('id')->contains($followId)) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_FOLLOW_HAVE_FOLLOWED,
            ]);
        }
        $user->following()->syncWithoutDetaching((array) $followId);

        return true;
    }

    public function unfollow(int $followId, int $userId): bool
    {
        $follower = $this->userRepository->getById($followId);
        if (empty($follower)) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_USER_NOT_EXIST,
            ]);
        }

        $user = $this->userRepository->getById($userId, [
            'following',
        ]);
        if (empty($user)) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_USER_NOT_EXIST,
            ]);
        }

        if ($user->id == $followId) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_UNFOLLOW_SELF,
            ]);
        }

        if (!$user->following->pluck('id')->contains($followId)) {
            throw app(CustomException::class, [
                'apiCode' => ApiResponseCode::ERROR_UNFOLLOW_NOT_FOLLOWED,
            ]);
        }
        $user->following()->detach((array) $followId);

        return true;
    }
}
