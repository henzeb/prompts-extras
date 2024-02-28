<?php

namespace Henzeb\Prompts;

use Henzeb\Prompts\Concerns\FakesInputOutput;
use Henzeb\Prompts\Concerns\Interactivity;
use Henzeb\Prompts\Concerns\Themes;
use Laravel\Prompts\Output\ConsoleOutput;
use Laravel\Prompts\Prompt as LaravelPrompt;
use Laravel\Prompts\Terminal;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Prompt extends LaravelPrompt
{
    use Interactivity;
    use FakesInputOutput;
    use Themes;

    /**
     * Get the value of the prompt.
     */
    public function value(): mixed
    {
        return throw new RuntimeException('This prompt does not have a value.');
    }

    public static function output(): OutputInterface
    {
        return LaravelPrompt::output();
    }

    public static function resetOutput(): void
    {
        LaravelPrompt::$output = new ConsoleOutput();
        LaravelPrompt::$terminal = new Terminal();
        LaravelPrompt::$validateUsing = null;
    }
}
