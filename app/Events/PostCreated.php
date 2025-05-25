<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * channel format.
     *
     * @var string
     */
    public const CHANNEL_FORMAT = 'new-post-from-user-%d';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public readonly Post $post)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn(): Channel|array
    {
        return new Channel(sprintf(static::CHANNEL_FORMAT, $this->post->user_id));
    }

    /**
     * broadcast with.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return $this->post->load([
            'user',
        ])->toArray();
    }
}
