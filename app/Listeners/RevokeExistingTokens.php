<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\UserRepository;

class RevokeExistingTokens
{
    /**
     * UserRepository
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = $this->userRepository->find($event->userId);
        if (null === $user) {
            return;
        }

        $user->tokens()
            ->offset(1)
            ->limit(\PHP_INT_MAX)
            ->get()
            ->map(function ($token) {
                $token->revoke();
        });
    }
}
