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
        $output = static::output();

        if (method_exists($output, 'getOutput')) {
            $output = $output->getOutput();
        }

        LaravelPrompt::interactive(
            match (is_object($output) ? $output::class : null) {
                BufferedOutput::class,
                PromptsBufferedConsoleOutput::class,
                BufferedConsoleOutput::class => false,
                default => stream_isatty(STDIN)
            }
        );
    }
}
