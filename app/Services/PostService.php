<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;

class PostService
{
    /**
     * PostRepository
     *
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * construct
     *
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
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
        $post = $this->postRepository->find($id);
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
        $post = $this->postRepository->find($id);
        if (null === $post) {
            return true;
        }

        if ((int)$post->user_id !== $userId) {
            return false;
        }
        $this->postRepository->del($id);

        return true;
    }
}
