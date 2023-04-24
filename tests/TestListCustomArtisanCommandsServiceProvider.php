<?php

namespace JakeBathman\ListCustomArtisanCommands\Tests;

use App\Console\Commands\SomeCustomCommand;
use Illuminate\Support\ServiceProvider;
use JakeBathman\ListCustomArtisanCommands\ListCustomArtisanCommands;

class TestListCustomArtisanCommandsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ListCustomArtisanCommands::class,
                SomeCustomCommand::class,
            ]);
        }
    }
}
