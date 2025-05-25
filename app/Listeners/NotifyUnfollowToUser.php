<?php

namespace App\Listeners;

use App\Events\UserFollowDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUnfollowToUser implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param UserFollowDeleted $event
     *
     * @return void
     */
    public function handle(UserFollowDeleted $event): void
    {
        \Log::info(sprintf('%s unfollow %s', data_get($event, 'user.name'), data_get($event, 'following.name')));

        return;
    }
}
