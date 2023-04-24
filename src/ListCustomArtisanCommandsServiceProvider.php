<?php

namespace JakeBathman\ListCustomArtisanCommands;

use Illuminate\Support\ServiceProvider;

class ListCustomArtisanCommandsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('list-custom-artisan-commands.php'),
            ], 'config');

            /*
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'list-custom-artisan-commands');

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/list-custom-artisan-commands'),
            ], 'views');
            */
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'list-custom-artisan-commands');
    }
}
