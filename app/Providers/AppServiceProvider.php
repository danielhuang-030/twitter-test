<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Models\BaseModel;
use App\Models\Post;
use App\Models\UserFollow;
use App\Observers\PostObserver;
use App\Observers\UserFollowObserver;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ignore passport routes
        Passport::ignoreRoutes();

        // custom exception handler
        $this->app->singleton(ExceptionHandler::class, function ($app) {
            return $app->make(Handler::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 設定訪問令牌過期時間為 2 小時
        Passport::tokensExpireIn(now()->addHours(2));

        // 設定刷新令牌過期時間為 1 天
        Passport::refreshTokensExpireIn(now()->addDays(1));

        // 設定個人訪問令牌過期時間為 6 個月
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // enabling eloquent "Strict Mode" in non-production environment
        BaseModel::shouldBeStrict(!$this->app->isProduction());

        // observe
        UserFollow::observe(UserFollowObserver::class);
        Post::observe(PostObserver::class);
    }
}
