<?php

namespace App\Listeners;

use App\Events\UserFollowCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyFollowingToUser implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param UserFollowCreated $event
     *
     * @return void
     */
    public function handle(UserFollowCreated $event): void
    {
        \Log::info(sprintf('%s following %s', data_get($event, 'user.name'), data_get($event, 'following.name')));

        return;
    }
}
