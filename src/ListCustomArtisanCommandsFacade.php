<?php

namespace JakeBathman\ListCustomArtisanCommands;

use Illuminate\Support\Facades\Facade;

class ListCustomArtisanCommandsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'list-custom-artisan-commands';
    }
}
