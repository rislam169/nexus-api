<?php

namespace App\Providers;

use App\Contracts\Repositories\ArticleRepository;
use App\Contracts\Repositories\UserRepository;
use App\Contracts\Repositories\UserSettingRepository;
use App\Repositories\ArticleRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\UserSettingRepositoryEloquent;
use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
        $this->app->bind(UserSettingRepository::class, UserSettingRepositoryEloquent::class);
        $this->app->bind(ArticleRepository::class, ArticleRepositoryEloquent::class);
    }
}
