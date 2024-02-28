<?php

namespace Tests\Unit\Illuminate\Stubs;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

use function Henzeb\Prompts\Input\addArgument;
use function Henzeb\Prompts\Input\addOption;
use function Henzeb\Prompts\Input\argument;
use function Henzeb\Prompts\Input\option;
use function Laravel\Prompts\note;

class SecondTestCommand extends Command
{
    protected $signature = 'second';

    protected $description = 'Command description';

    protected function configure(): void
    {
        addArgument('engine');
        addOption('enable');
    }

    public function handle(): void
    {
        if (option('enable')) {
            Artisan::call('second', ['engine' => 'steam']);
        }

        note('engine ' . argument('engine'));
    }
}
