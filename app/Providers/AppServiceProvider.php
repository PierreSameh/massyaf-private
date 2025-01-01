<?php

namespace App\Providers;

use App\Models\Chat;
use App\Observers\ChatObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Chat::observe(ChatObserver::class);
    }
}
