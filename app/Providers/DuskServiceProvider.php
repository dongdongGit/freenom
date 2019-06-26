<?php

namespace App\Providers;

use Laravel\Dusk\Browser;
use Illuminate\Support\ServiceProvider;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register the Dusk's browser macros.
     *
     * @return void
     */
    public function boot()
    {
        Browser::macro('reload', function ($element = null) {
            $this->script("location.reload();");

            return $this;
        });
    }
}