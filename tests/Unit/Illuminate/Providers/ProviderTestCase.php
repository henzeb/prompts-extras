<?php

namespace Tests\Unit\Illuminate\Providers;

use Henzeb\Prompts\Illuminate\Providers\PromptsExtrasProvider;
use Illuminate\Console\Application as Artisan;
use Illuminate\Foundation\Testing\WithConsoleEvents;
use Orchestra\Testbench\TestCase;
use Tests\Unit\Illuminate\Stubs\FirstTestCommand;
use Tests\Unit\Illuminate\Stubs\SecondTestCommand;

class ProviderTestCase extends TestCase
{
    use WithConsoleEvents;

    protected function getPackageProviders($app): array
    {
        return [
            PromptsExtrasProvider::class
        ];
    }

    public function registerCommands(): void
    {
        Artisan::starting(function ($artisan) {
            $artisan->resolveCommands([
                FirstTestCommand::class,
                SecondTestCommand::class,
            ]);
        });
    }
}
