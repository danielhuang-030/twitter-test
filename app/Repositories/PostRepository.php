<?php

namespace App\Repositories;

use App\Models\Post;
use App\Params\PostParam;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository
{
    /**
     * Post.
     *
     * @var Post
     */
    private $post;

    /**
     * construct.
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
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
        $requestData['user_id'] = $userId;

        return $this->post->create($requestData);
    }

    /**
     * edit.
     *
     * @param array $requestData
     * @param int   $id
     *
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
     * find.
     *
     * @param array $requestData
     * @param int   $userId
     *
     * @return Post
     */
    public function find(int $id)
    {
        return $this->post->find($id);
    }

    /**
     * del.
     *
     * @param int $id
     *
     * @return int
     */
    public function del(int $id)
    {
        return $this->post->find($id)->delete();
    }

    /**
     * get by param.
     *
     * @param PostParam $param
     *
     * @return LengthAwarePaginator
     */
    public function getByParam(PostParam $param)
    {
        // query
        $query = $this->post->query();

        // user id
        $userId = $param->getUserId();
        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        // withs
        $withs = $param->getWiths();
        if (!empty($withs)) {
            $query->with($withs);
        }

        // sort
        $sortBy = $param->getSortBy();
        if (!empty($sortBy)) {
            foreach ($sortBy as $sort => $isDesc) {
                $query->orderBy($sort, $isDesc ? 'desc' : 'asc');
            }
        }

        return $query->paginate($param->getPerPage(), ['*'], 'page', $param->getPage());
    }
}
