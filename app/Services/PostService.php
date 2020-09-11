<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;

class PostService
{
    /**
     * PostRepository.
     *
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * construct.
     *
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * add.
     *
     * @param array $requestData
     * @param int   $userId
     *
     * @return Post
     */
    public function add(array $requestData, int $userId)
    {
        return $this->postRepository->add($requestData, $userId);
    }

    /**
     * find.
     *
     * @param int $id
     *
     * @return Post
     */
    public function find(int $id)
    {
        return $this->postRepository->find($id);
    }

    /**
     * edit.
     *
     * @param array $requestData
     * @param int   $id
     * @param int   $userId
     *
     * @return Post
     */
    public function edit(array $requestData, int $id, int $userId)
    {
        $post = $this->find($id);
        if (null === $post) {
            return null;
        }
        if ((int) $post->user_id !== $userId) {
            return null;
        }

        return $this->postRepository->edit($requestData, $id);
    }

    /**
     * del.
     *
     * @param int $id
     * @param int $userId
     *
     * @return bool
     */
    public function del(int $id, int $userId)
    {
        $post = $this->find($id);
        if (null === $post) {
            return false;
        }
        if ((int) $post->user_id !== $userId) {
            return false;
        }
        if (0 === $this->postRepository->del($id)) {
            return false;
        }

        return true;
    }

    /**
     * like.
     *
     * @param int $id
     * @param int $userId
     *
     * @return bool
     */
    public function like(int $id, int $userId)
    {
        $post = $this->find($id);
        if (null === $post || $post->user_id === $userId) {
            return false;
        }
        $post->likedUsers()->syncWithoutDetaching((array) $userId);

        return true;
    }

    /**
     * dislike.
     *
     * @param int $id
     * @param int $userId
     *
     * @return bool
     */
    public function dislike(int $id, int $userId)
    {
        $post = $this->find($id);
        if (null === $post || $post->user_id === $userId) {
            return false;
        }
        $post->likedUsers()->detach((array) $userId);

        return true;
    }
}
