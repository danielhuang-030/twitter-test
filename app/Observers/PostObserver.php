<?php

namespace App\Observers;

use App\Events\PostCreated;
use App\Models\Post;

class PostObserver
{
    /**
     * Handle the post "created" event.
     *
     * @param Post $post
     *
     * @return void
     */
    public function created(Post $post)
    {
        event(new PostCreated($post));
    }

    /**
     * Handle the post "updated" event.
     *
     * @param Post $post
     *
     * @return void
     */
    public function updated(Post $post)
    {
    }

    /**
     * Handle the post "deleting" event.
     *
     * @param Post $post
     *
     * @return void
     */
    public function deleting(Post $post)
    {
        // delete like post association
        $post->likedUsers()->detach();
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param Post $post
     *
     * @return void
     */
    public function deleted(Post $post)
    {
    }

    /**
     * Handle the post "restored" event.
     *
     * @param Post $post
     *
     * @return void
     */
    public function restored(Post $post)
    {
    }

    /**
     * Handle the post "force deleted" event.
     *
     * @param Post $post
     *
     * @return void
     */
    public function forceDeleted(Post $post)
    {
    }
}
