<?php

namespace App\Listeners;

use App\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Token;

class RevokeExistingTokens implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * limit.
     *
     * @var int
     */
    public const LIMIT = 100;

    /**
     * Create the event listener.
     */
    public function __construct(protected readonly UserRepository $userRepository)
    {
    }

    /**
     * Handle the event.
     *
     * @param AccessTokenCreated $event
     *
     * @return void
     */
    public function handle(AccessTokenCreated $event): void
    {
        $user = $this->userRepository->getById($event->userId);
        if (empty($user)) {
            return;
        }

        $user->tokens()
            ->offset(1)
            ->limit(static::LIMIT)
            ->get()
            ->map(function (\Laravel\Passport\Token $token): void {
                $token->delete();
            });
    }
}
