<?php

namespace App\Observers;

use App\Events\PostCreated;
use App\Models\Post;

class PostObserver
{
    /**
     * Handle the award setting "saved" event.
     *
     * @param Post $post
     * @return void
     */
    public function saved(Post $post)
    {
        event(new PostCreated($post));
    }
}
