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
    public const CHANNEL_FORMAT = 'new-user-unfollow-user-%d';

    public readonly \App\Models\User $user;
    public readonly \App\Models\User $following;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserFollow $userFollow)
    {
        // Assuming $userFollow->user and $userFollow->following always return non-null User instances
        $this->user = $userFollow->user;
        $this->following = $userFollow->following;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn(): Channel|array
    {
        return new Channel(sprintf(static::CHANNEL_FORMAT, $this->following->id));
    }

    /**
     * broadcast with.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return $this->user->toArray();
    }
}
