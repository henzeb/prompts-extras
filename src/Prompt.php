<?php

namespace Henzeb\Prompts;

use Closure;
use Henzeb\Prompts\Concerns\FakesInputOutput;
use Henzeb\Prompts\Concerns\Interactivity;
use Henzeb\Prompts\Concerns\Pcntl;
use Henzeb\Prompts\Concerns\Themes;
use Henzeb\Prompts\Illuminate\Validation\Validator;
use Henzeb\Prompts\Illuminate\Validation\ValidatorFactory;
use Laravel\Prompts\Output\ConsoleOutput;
use Laravel\Prompts\Prompt as LaravelPrompt;
use Laravel\Prompts\Terminal;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Prompt extends LaravelPrompt
{
    use Interactivity;
    use Pcntl;
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

    public static function validateUsing(Closure $callback): void
    {
        if (!empty(parent::$validateUsing)) {
            $validateUsing = clone parent::$validateUsing;
            parent::validateUsing(
                function ($value) use ($validateUsing, $callback) {
                    return $callback($value) ?? $validateUsing($value);
                }
            );
            return;
        }

        parent::validateUsing($callback);
    }

    public static function useLaravelValidator(
        string $locale = 'en',
        ?string $path = null,
        ?string $fallback = null
    ): void
    {
        self::validateUsing(
            (new Validator(
                new ValidatorFactory($locale, $path, $fallback),
            ))(...)
        );
    }

    public static function resetOutput(): void
    {
        LaravelPrompt::$output = new ConsoleOutput();
        LaravelPrompt::$terminal = new Terminal();
        LaravelPrompt::$validateUsing = null;
    }
}
