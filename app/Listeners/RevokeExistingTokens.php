<?php

namespace App\Listeners;

use App\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Passport\Events\AccessTokenCreated;

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
     * UserRepository.
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create the event listener.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param AccessTokenCreated $event
     *
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        $user = $this->userRepository->getById($event->userId);
        if (null === $user) {
            return;
        }

        $user->tokens()
            ->offset(1)
            ->limit(static::LIMIT)
            ->get()
            ->map(function ($token) {
                $token->delete();
            });
    }
}
