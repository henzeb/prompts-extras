<?php

namespace Tests\Unit\Illuminate\Stubs;

use Illuminate\Console\Command;
use function Henzeb\Prompts\Input\addArgument;
use function Henzeb\Prompts\Input\addOption;


class FirstTestCommand extends Command
{
    protected $signature = 'first';

    protected $description = 'Command description';

    protected function configure(): void
    {
        addArgument('name');
        addOption(
            'confirm',
            validateEarly: true,
            prompt: 'confirm',
            options: [
                'required' => true,
                'hint' => 'Select yes to continue.'
            ]
        );
    }

    public function handle(): void
    {

    }
}
