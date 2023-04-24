<?php

namespace JakeBathman\ListCustomArtisanCommands\Tests;

use Illuminate\Support\Facades\Artisan;
use JakeBathman\ListCustomArtisanCommands\ListCustomArtisanCommandsServiceProvider;

class EmptyTest extends TestCase
{
    /** @test */
    public function command_runs()
    {
        Artisan::call('list-custom');

        $this->assertStringContainsString('There are no custom commands defined.', Artisan::output());
    }

    protected function getPackageProviders($app)
    {
        return [
            ListCustomArtisanCommandsServiceProvider::class,
        ];
    }
}
