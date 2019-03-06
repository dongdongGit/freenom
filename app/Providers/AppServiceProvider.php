<?php

namespace App\Providers;

use App\Models\Image;
use App\Models\Domain;
use App\Observers\ImageObserver;
use App\Observers\DomainObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
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
        //
    }
}
