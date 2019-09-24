<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    /**
     * IS LIKED LIKE
     *
     * @var int
     */
    const IS_LIKED_LIKE = 1;

    /**
     * IS LIKED DISLIKE
     *
     * @var int
     */
    const IS_LIKED_DISLIKE = 0;

    protected $guarded = [];
}