<?php

namespace App\Events;

use App\Models\UserFollow;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserFollowDeleted implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * channel format.
     *
     * @var string
     */
    const CHANNEL_FORMAT = 'new-user-unfollow-user-%d';

    /**
     * user.
     *
     * @var User
     */
    public $user;

    /**
     * following.
     *
     * @var User
     */
    public $following;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserFollow $userFollow)
    {
        $this->user = $userFollow->user;
        $this->following = $userFollow->following;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel(sprintf(static::CHANNEL_FORMAT, $this->following->id));
    }

    /**
     * broadcast with.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return $this->user->toArray();
    }
}
