<?php

namespace TheCodeRepublic\Tablavel;

use Illuminate\Support\ServiceProvider;
use TheCodeRepublic\Tablavel\Commands\TablavelCommand ;

class TablavelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            TablavelCommand::class,
        ]);
    }
}
