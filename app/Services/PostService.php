<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use App\Repositories\PostLikeRepository;

class PostService
{
    /**
     * PostRepository
     *
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * PostLikeRepository
     *
     * @var PostLikeRepository
     */
    protected $postLikeRepository;

    /**
     * construct
     *
     * @param PostRepository $postRepository
     * @param PostLikeRepository $postLikeRepository
     */
    public function __construct(PostRepository $postRepository, PostLikeRepository $postLikeRepository)
    {
        $this->postRepository = $postRepository;
        $this->postLikeRepository = $postLikeRepository;
    }

    /**
     * add
     *
     * @param array $requestData
     * @param integer $userId
     * @return Post
     */
    public function add(array $requestData, int $userId)
    {
        return $this->postRepository->add($requestData, $userId);
    }

    /**
     * find
     *
     * @param integer $id
     * @return Post
     */
    public function find(int $id)
    {
        return $this->postRepository->find($id);
    }

    /**
     * edit
     *
     * @param array $requestData
     * @param integer $id
     * @param integer $userId
     * @return Post
     */
    public function edit(array $requestData, int $id, int $userId)
    {
        $post = $this->find($id);
        if (null === $post) {
            return null;
        }

        if ((int)$post->user_id !== $userId) {
            return null;
        }

        return $this->postRepository->edit($requestData, $id);
    }

    /**
     * del
     *
     * @param integer $id
     * @param integer $userId
     * @return bool
     */
    public function del(int $id, int $userId)
    {
        $post = $this->find($id);
        if (null === $post) {
            return true;
        }

        if ((int)$post->user_id !== $userId) {
            return false;
        }
        $this->postRepository->del($id);

        return true;
    }

    /**
     * like
     *
     * @param integer $postId
     * @param integer $userId
     * @return bool
     */
    public function like(int $postId, int $userId)
    {
        $post = $this->find($postId);
        if (null === $post || $post->user_id === $userId) {
            return false;
        }

        $postLike = $this->postLikeRepository->like($postId, $userId);
        if (null === $postLike) {
            return false;
        }
        return true;
    }

    /**
     * dislike
     *
     * @param integer $postId
     * @param integer $userId
     * @return bool
     */
    public function dislike(int $postId, int $userId)
    {
        $post = $this->find($postId);
        if (null === $post || $post->user_id === $userId) {
            return false;
        }

        $postLike = $this->postLikeRepository->dislike($postId, $userId);
        if (null === $postLike) {
            return false;
        }
        return true;
    }
}
