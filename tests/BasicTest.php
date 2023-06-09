<?php

namespace JakeBathman\ListCustomArtisanCommands\Tests;

use Illuminate\Support\Facades\Artisan;

class BasicTest extends TestCase
{
    /** @test */
    public function command_runs()
    {
        Artisan::call('list-custom');

        $this->assertStringContainsString('Available commands [inside App\ namespace]', Artisan::output());
    }

    protected function getPackageProviders($app)
    {
        return [
            TestListCustomArtisanCommandsServiceProvider::class,
        ];
    }
}
