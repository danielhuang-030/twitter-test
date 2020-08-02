<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\UserFollow;
use App\Observers\PostObserver;
use App\Observers\UserFollowObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // observe
        UserFollow::observe(UserFollowObserver::class);
        Post::observe(PostObserver::class);
    }
}
