<?php

namespace App\Providers;

use App\Interfaces\News\NewsServiceInterface;
use Illuminate\Support\ServiceProvider;
use App\Services\News\NewsService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(NewsServiceInterface::class, NewsService::class);
    }
}
