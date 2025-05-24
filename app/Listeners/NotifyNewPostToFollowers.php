<?php

namespace App\Listeners;

use App\Events\PostCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyNewPostToFollowers implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param PostCreated $event
     *
     * @return void
     */
    public function handle(PostCreated $event): void
    {
        $followers = data_get($event, 'post.user.followers', collect());
        if ($followers->isEmpty()) {
            return;
        }
        $followers->each(function (\App\Models\User $user) use ($event): void {
            Log::info(sprintf('%s add a new post, notify %s', data_get($event, 'post.user.name', ''), $user->name));
        });

        return;
    }
}
