<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SomeCustomCommand extends Command
{
    protected $signature = 'some:command';
    protected $description = 'An example of a custom command in the app.';

    public function handle()
    {
        $this->info('This is an example of a custom command in the app.');

        return Command::SUCCESS;
    }
}
