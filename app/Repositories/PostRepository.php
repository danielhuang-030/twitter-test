<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    /**
     * Post
     *
     * @var Post
     */
    private $post;

    /**
     * construct
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
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
        $requestData['user_id'] = $userId;

        return $this->post->create($requestData);
    }

    /**
     * edit
     *
     * @param array $requestData
     * @param integer $id
     * @return Post
     */
    public function edit(array $requestData, int $id)
    {
        $post = $this->post->find($id);
        if (null === $post) {
            return null;
        }
        if (!$post->update($requestData)) {
            return null;
        }

        return $post;
    }

    /**
     * find
     *
     * @param array $requestData
     * @param integer $userId
     * @return Post
     */
    public function find(int $id)
    {
        return $this->post->find($id);
    }

    /**
     * del
     *
     * @param integer $id
     * @return int
     */
    public function del(int $id)
    {
        return $this->post->find($id)->delete();
    }
}
