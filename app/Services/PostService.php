<?php

namespace App\Services;

use App\Models\Post;
use App\Params\PostParam;
use App\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    public function __construct(protected PostRepository $postRepository)
    {
    }

    public function getPosts(PostParam $param): LengthAwarePaginator
    {
        return $this->postRepository->getPaginatorByParam($param);
    }

    public function add(array $requestData, int $userId): ?Post
    {
        $requestData['user_id'] = $userId;

        $post = $this->postRepository->create($requestData);
        if (empty($post)) {
            return null;
        }

        return $post->load([
            'user',
        ]);
    }

    public function find(int $id): ?Post
    {
        return $this->postRepository->getById($id, [
            'user',
        ]);
    }

    public function edit(array $requestData, int $id, int $userId): ?Post
    {
        $post = $this->find($id);
        if (empty($post)) {
            return null;
        }
        if ($post->user_id != $userId) {
            return null;
        }

        return $this->postRepository->update($requestData, $id);
    }

    public function del(int $id, int $userId): bool
    {
        $post = $this->find($id);
        if (empty($post)) {
            return false;
        }
        if ($post->user_id != $userId) {
            return false;
        }
        $this->postRepository->delete($id);

        return true;
    }

    public function like(int $id, int $userId): bool
    {
        $post = $this->find($id);
        if (empty($post) || $post->user_id == $userId) {
            return false;
        }
        $post->likedUsers()->syncWithoutDetaching((array) $userId);

        return true;
    }

    public function dislike(int $id, int $userId): bool
    {
        $post = $this->find($id);
        if (empty($post) || $post->user_id == $userId) {
            return false;
        }
        $post->likedUsers()->detach((array) $userId);

        return true;
    }
}
