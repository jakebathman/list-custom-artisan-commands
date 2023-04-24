<?php

namespace JakeBathman\ListCustomArtisanCommands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Descriptor\ApplicationDescription;
use Symfony\Component\Console\Descriptor\TextDescriptor;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Output\OutputInterface;

class ListCustomArtisanCommands extends Command
{
    protected $signature = 'list-custom';
    protected $description = 'List only custom Artisan commands';

    protected $application;
    protected $appNamespace;
    protected $commands = [];
    protected $namespaces = [];

    public function handle()
    {
        $this->application = $this->getApplication();
        $this->appNamespace = app()->getNamespace();

        // Get all custom commands registered in the container
        $allCommands = collect($this->application->all())
            ->filter(function ($command) {
                return Str::startsWith(get_class($command), $this->appNamespace);
            })
            ->filter()
            ->toArray();

        foreach ($this->sortCommands($allCommands) as $namespace => $commands) {
            $names = [];

            /** @var Command $command */
            foreach ($commands as $name => $command) {
                // Exclude nameless commands and this command
                if (! $command->getName() || ($command->getName() === $this->signature)) {
                    continue;
                }

                $this->commands[$name] = $command;

                $names[] = $name;
            }

            if (empty($names)) {
                // A namespace might have had all of its commands filtered out
                continue;
            }

            $this->namespaces[$namespace] = ['id' => $namespace, 'commands' => $names];
        }

        $this->writeCommands($this->commands, ['format' => 'txt']);

        return Command::SUCCESS;
    }

    public function writeCommands(array $commands, array $options = [])
    {
        $textDescriptor = new TextDescriptor;

        if ('' != $help = $this->application->getHelp()) {
            $this->writeText("{$help}\n\n", $options);
        }

        // calculate max. width based on available commands per namespace
        $width = $this->getColumnWidth(array_merge(...array_values(array_map(function ($namespace) {
            return array_intersect($namespace['commands'], array_keys($this->commands));
        }, array_values($this->namespaces)))));

        $this->writeText("<comment>Available commands [inside {$this->appNamespace} namespace]:</comment>\n", $options);

        // Stop here if there are no commands to display
        if (empty($this->namespaces)) {
            $this->writeText("\n");
            $this->writeText("  There are no custom commands defined.\n");
        }

        foreach ($this->namespaces as $namespace) {
            $namespace['commands'] = array_filter($namespace['commands'], function ($name) {
                return isset($this->commands[$name]);
            });

            if (ApplicationDescription::GLOBAL_NAMESPACE !== $namespace['id']) {
                $this->writeText("\n");
                $this->writeText(' <comment>' . $namespace['id'] . '</comment>', $options);
            }

            foreach ($namespace['commands'] as $name) {
                $this->writeText("\n");
                $spacingWidth = $width - Helper::width($name);
                $command = $this->commands[$name];
                $this->writeText(
                    sprintf(
                        '  <info>%s</info>%s%s',
                        $name,
                        str_repeat(' ', $spacingWidth),
                        $command->getDescription()
                    ),
                    $options
                );
            }

            $this->writeText("\n");
        }
    }

    public function sortCommands(array $commands): array
    {
        $namespacedCommands = [];
        $globalCommands = [];
        $sortedCommands = [];
        foreach ($commands as $name => $command) {
            $key = $this->application->extractNamespace($name, 1);
            if (in_array($key, ['', ApplicationDescription::GLOBAL_NAMESPACE], true)) {
                $globalCommands[$name] = $command;
            } else {
                $namespacedCommands[$key][$name] = $command;
            }
        }

        if ($globalCommands) {
            ksort($globalCommands);
            $sortedCommands[ApplicationDescription::GLOBAL_NAMESPACE] = $globalCommands;
        }

        if ($namespacedCommands) {
            ksort($namespacedCommands, SORT_STRING);
            foreach ($namespacedCommands as $key => $commandsSet) {
                ksort($commandsSet);
                $sortedCommands[$key] = $commandsSet;
            }
        }

        return $sortedCommands;
    }

    protected function writeText(string $content, array $options = [])
    {
        $this->output->write(messages: $content, newline: false, options: OutputInterface::OUTPUT_NORMAL);
    }

    /**
     * @param  array<Command|string>  $commands
     */
    protected function getColumnWidth(array $commands): int
    {
        $widths = [];

        foreach ($commands as $command) {
            if ($command instanceof Command) {
                $widths[] = Helper::width($command->getName());
            } else {
                $widths[] = Helper::width($command);
            }
        }

        return $widths ? max($widths) + 2 : 0;
    }

    /**
     * @param  InputOption[]  $options
     */
    protected function calculateTotalWidthForOptions(array $options): int
    {
        $totalWidth = 0;
        foreach ($options as $option) {
            // "-" + shortcut + ", --" + name
            $nameLength = 1 + max(Helper::width($option->getShortcut()), 1) + 4 + Helper::width($option->getName());
            if ($option->isNegatable()) {
                $nameLength += 6 + Helper::width($option->getName()); // |--no- + name
            } elseif ($option->acceptValue()) {
                $valueLength = 1 + Helper::width($option->getName()); // = + value
                $valueLength += $option->isValueOptional() ? 2 : 0; // [ + ]

                $nameLength += $valueLength;
            }
            $totalWidth = max($totalWidth, $nameLength);
        }

        return $totalWidth;
    }
}
