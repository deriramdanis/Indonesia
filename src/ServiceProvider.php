<?php

namespace Laravolt\Indonesia;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->bind('indonesia', function() {
            return new Indonesia;
        });
        $this->commands(\Laravolt\Indonesia\Commands\IndonesiaCommand::class);
    }

    public function boot()
    {
        //require __DIR__ . '/Http/routes.php';

        $this->publishes([
	        __DIR__ . '/migrations' => $this->app->databasePath() . '/migrations'
	    ], 'migrations');


    }
}