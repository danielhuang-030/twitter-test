<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PostLike extends Pivot
{
    /**
     * IS LIKED DISLIKE.
     *
     * @var int
     */
    const LIKED_DISLIKE = 0;

    /**
     * IS LIKED LIKE.
     *
     * @var int
     */
    const LIKED_LIKE = 1;
}
