<?php

namespace App\Repositories;

use App\Models\PostLike;

class PostLikeRepository
{
    /**
     * PostLike
     *
     * @var PostLike
     */
    private $postLike;

    /**
     * construct
     *
     * @param PostLike $postLike
     */
    public function __construct(PostLike $postLike)
    {
        $this->postLike = $postLike;
    }

    /**
     * get by post id, user id
     *
     * @param integer $postId
     * @param integer $userId
     * @return PostLike
     */
    public function getByPostIdAndUserId(int $postId, int $userId)
    {
        return $this->postLike->firstOrCreate([
            'post_id' => $postId,
            'user_id' => $userId,
        ]);
    }

    /**
     * like
     *
     * @param integer $postId
     * @param integer $userId
     * @return PostLike
     */
    public function like(int $postId, int $userId)
    {
        return $this->postLike->updateOrCreate([
            'post_id' => $postId,
            'user_id' => $userId,
        ], [
            'is_liked' => PostLike::IS_LIKED_LIKE,
        ]);
    }

    /**
     * dislike
     *
     * @param integer $postId
     * @param integer $userId
     * @return PostLike
     */
    public function dislike(int $postId, int $userId)
    {
        return $this->postLike->updateOrCreate([
            'post_id' => $postId,
            'user_id' => $userId,
        ], [
            'is_liked' => PostLike::IS_LIKED_DISLIKE,
        ]);
    }
}
