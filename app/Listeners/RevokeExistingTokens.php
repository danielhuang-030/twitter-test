<?php

namespace App\Listeners;

use App\Repositories\UserRepository;
use Laravel\Passport\Events\AccessTokenCreated;

class RevokeExistingTokens
{
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
        $user = $this->userRepository->find($event->userId);
        if (null === $user) {
            return;
        }

        $user->tokens()
            ->offset(1)
            ->limit(\PHP_INT_MAX)
            ->get()
            ->map(function ($token) {
                $token->delete();
            });
    }
}
