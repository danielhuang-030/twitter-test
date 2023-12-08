<?php

namespace App\Services;

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\Post;
use App\Params\PostParam;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    public function __construct(
        protected PostRepository $postRepository,
        protected UserRepository $userRepository
    ) {
    }

    public function getPosts(PostParam $param): LengthAwarePaginator
    {
        return $this->postRepository->getPaginatorByParam($param);
    }

    public function getUserLikedPostIds(int $userId, array $postIds): array
    {
        return $this->postRepository->getUserLikedPostsByUserIdAndPostIds($userId, $postIds)
            ->pluck('id')
            ->toArray();
    }

    public function getFollowedUserIds(int $userId, array $authorIds): array
    {
        return $this->userRepository->getUserFollowedAuthorsByUserIdAndAuthorIds($userId, $authorIds)
            ->pluck('id')
            ->toArray();
    }

    public function add(array $requestData, int $userId): ?Post
    {
        $requestData['user_id'] = $userId;

        $post = $this->postRepository->create($requestData);
        if (empty($post)) {
            throw app(CustomException::class, ['apiCode' => ApiResponseCode::ERROR_POST_ADD]);

            return null;
        }

        return $post->load([
            'user',
        ]);
    }

    public function find(int $id): ?Post
    {
        $post = $this->postRepository->getById($id, [
            'user',
        ]);

        if (empty($post)) {
            throw app(CustomException::class, ['apiCode' => ApiResponseCode::ERROR_POST_NOT_EXIST]);

            return null;
        }

        return $post;
    }

    public function edit(array $requestData, int $id, int $userId): ?Post
    {
        $post = $this->find($id);

        if ($post->user_id != $userId) {
            throw app(CustomException::class, ['apiCode' => ApiResponseCode::ERROR_POST_NOT_AUTHOR]);

            return null;
        }

        $postUpdated = $this->postRepository->update($requestData, $id);
        if (empty($postUpdated)) {
            throw app(CustomException::class, ['apiCode' => ApiResponseCode::ERROR_POST_EDIT]);

            return null;
        }

        return $postUpdated;
    }

    public function del(int $id, int $userId): bool
    {
        $post = $this->find($id);

        if ($post->user_id != $userId) {
            throw app(CustomException::class, ['apiCode' => ApiResponseCode::ERROR_POST_NOT_AUTHOR]);

            return false;
        }
        $this->postRepository->delete($id);

        return true;
    }

    public function like(int $id, int $userId): bool
    {
        $post = $this->find($id);

        if ($post->user_id == $userId) {
            throw app(CustomException::class, ['apiCode' => ApiResponseCode::ERROR_POST_AUTHOR_CAN_NOT_LIKE]);

            return false;
        }
        $post->likedUsers()->syncWithoutDetaching((array) $userId);

        return true;
    }

    public function dislike(int $id, int $userId): bool
    {
        $post = $this->find($id);

        if ($post->user_id == $userId) {
            throw app(CustomException::class, ['apiCode' => ApiResponseCode::ERROR_POST_AUTHOR_CAN_NOT_LIKE]);

            return false;
        }
        $post->likedUsers()->detach((array) $userId);

        return true;
    }
}
