<?php

namespace App\Providers;

use App\Models\Image;
use App\Models\Domain;
use App\Observers\ImageObserver;
use App\Observers\DomainObserver;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;
use Illuminate\Support\Facades\Redis;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->isLocal()) {
            Redis::enableEvents();
        }

        Image::observe(ImageObserver::class);
        Domain::observe(DomainObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(TelescopeServiceProvider::class);
        }
        $this->app->register(DuskServiceProvider::class);
    }
}
