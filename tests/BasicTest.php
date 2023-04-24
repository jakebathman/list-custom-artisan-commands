<?php

namespace JakeBathman\ListCustomArtisanCommands\Tests;

use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;

class BasicTest extends TestCase
{
    /** @test */
    public function command_runs()
    {
        Artisan::call('list-custom');
        $output = Artisan::output();
        $this->assertStringContainsString('Available commands [inside App\ namespace]', $output);
    }

    protected function getPackageProviders($app)
    {
        return ['JakeBathman\ListCustomArtisanCommands\ListCustomArtisanCommandsServiceProvider'];
    }
}
