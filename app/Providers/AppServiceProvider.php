<?php

namespace App\Providers;

use App\Interfaces\News\NewsServiceInterface;
use App\Services\News\NewsService;
use App\Interfaces\Users\UserRepositoryInterface;
use App\Interfaces\Users\UserServiceInterface;
use App\Services\Users\UserService;
use App\Repositories\Users\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(NewsServiceInterface::class, NewsService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
