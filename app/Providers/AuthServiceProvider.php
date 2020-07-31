<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * expire days.
     *
     * @var int
     */
    const EXPIRE_DAYS = 14;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // passport routes
        // Passport::routes();

        $expireTime = now()->addDays(static::EXPIRE_DAYS);
        Passport::tokensExpireIn($expireTime);
        Passport::refreshTokensExpireIn($expireTime);
        Passport::personalAccessTokensExpireIn($expireTime);
    }
}
