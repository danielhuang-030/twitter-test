<?php

namespace App\Events;

use App\Models\UserFollow;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserFollowCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * channel format.
     *
     * @var string
     */
    public const CHANNEL_FORMAT = 'new-user-following-user-%d';

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
        // If they can be null, the property types should be ?User and checks added.
        // Based on typical Eloquent relationships, they should be User instances or null if not loaded/set.
        // For an event like UserFollowCreated, it's reasonable to assume these are valid.
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
