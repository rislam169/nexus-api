<?php

namespace App\Providers;

use App\Contracts\Service\ArticleContact;
use App\Contracts\Service\UserContact;
use App\Contracts\Service\UserSettingContact;
use App\Services\ArticleService;
use App\Services\UserService;
use App\Services\UserSettingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserContact::class, UserService::class);
        $this->app->bind(UserSettingContact::class, UserSettingService::class);
        $this->app->bind(ArticleContact::class, ArticleService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
