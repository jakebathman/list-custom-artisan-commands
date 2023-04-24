<?php

namespace JakeBathman\ListCustomArtisanCommands;

use Illuminate\Support\ServiceProvider;

class ListCustomArtisanCommandsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ListCustomArtisanCommands::class,
            ]);
        }
    }
}
