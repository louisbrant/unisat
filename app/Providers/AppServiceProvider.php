<?php

namespace App\Providers;

use App\Models\Chat;
use App\Models\Image;
use App\Models\Template;
use App\Observers\ChatObserver;
use App\Observers\ImageObserver;
use App\Observers\TemplateObserver;
use App\Observers\UserObserver;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Fix for utf8mb migration @https://laravel.com/docs/master/migrations#creating-indexes
        Schema::defaultStringLength(191);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        User::observe(UserObserver::class);
        Template::observe(TemplateObserver::class);
        Image::observe(ImageObserver::class);
        Chat::observe(ChatObserver::class);
    }
}
