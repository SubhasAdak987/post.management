<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Post;
use App\Models\PostComment;

class StatisticsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('App\Services\StatisticsService', function ($app) {
            return new \App\Services\StatisticsService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $view->with('stats', [
                'users' => User::count(),
                'posts' => Post::count(),
                'comments' => PostComment::count(),
            ]);
        });
    }
}
