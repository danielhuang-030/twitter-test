<?php

namespace App\Observers;

use App\Events\UserFollowCreated;
use App\Events\UserFollowDeleted;
use App\Models\UserFollow;

class UserFollowObserver
{
    /**
     * Handle the user follow "created" event.
     *
     * @param \App\Models\UserFollow $userFollow
     *
     * @return void
     */
    public function created(UserFollow $userFollow)
    {
        event(new UserFollowCreated($userFollow));
    }

    /**
     * Handle the user follow "updated" event.
     *
     * @param \App\Models\UserFollow $userFollow
     *
     * @return void
     */
    public function updated(UserFollow $userFollow)
    {
    }

    /**
     * Handle the user follow "deleted" event.
     *
     * @param \App\Models\UserFollow $userFollow
     *
     * @return void
     */
    public function deleted(UserFollow $userFollow)
    {
        event(new UserFollowDeleted($userFollow));
    }

    /**
     * Handle the user follow "restored" event.
     *
     * @param \App\Models\UserFollow $userFollow
     *
     * @return void
     */
    public function restored(UserFollow $userFollow)
    {
    }

    /**
     * Handle the user follow "force deleted" event.
     *
     * @param \App\Models\UserFollow $userFollow
     *
     * @return void
     */
    public function forceDeleted(UserFollow $userFollow)
    {
    }
}
