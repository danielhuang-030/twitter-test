<?php

namespace App\Listeners;

use App\Events\PostCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyFollowers implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param PostCreated $event
     * @return void
     */
    public function handle(PostCreated $event)
    {
        $post      = $event->post;
        $user      = $post->user;
        $followers = $user->followMes;
        if (0 === $followers->count()) {
            return;
        }
        foreach ($followers as $follower) {
            Log::info(sprintf('%s add a new post, notify %s', $user->name, $follower->name));
        }

        return;
    }
}
