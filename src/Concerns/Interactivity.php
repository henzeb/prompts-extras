<?php

namespace Henzeb\Prompts\Concerns;

use Illuminate\Console\BufferedConsoleOutput;
use Laravel\Prompts\Output\BufferedConsoleOutput as PromptsBufferedConsoleOutput;
use Laravel\Prompts\Prompt as LaravelPrompt;
use Symfony\Component\Console\Output\BufferedOutput;

trait Interactivity
{
    public static function interactivityEnabled(): bool
    {
        return static::$interactive;
    }
    public static function updateInteractivity(): void
    {
        $output = method_exists(static::output(), 'getOutput')
            ? static::output()->getOutput()
            : static::output();

        LaravelPrompt::interactive(
            match ($output::class) {
                BufferedOutput::class,
                PromptsBufferedConsoleOutput::class,
                BufferedConsoleOutput::class => false,
                default => stream_isatty(STDIN)
            }
        );
    }
}
