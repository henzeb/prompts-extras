<?php

namespace Henzeb\Prompts;

use Henzeb\Prompts\Concerns\InputArguments;
use Henzeb\Prompts\Concerns\InputDefinitions;
use Henzeb\Prompts\Concerns\InputOptions;
use Henzeb\Prompts\Inputs\ConsoleInput;
use Henzeb\Prompts\Support\Stacks\CurrentCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\Input;

class Inputs
{
    use InputDefinitions;
    use InputArguments;
    use InputOptions;

    protected static CurrentCommand $command;

    protected static array $inputs = [];

    public static function setInput(Input $inputs): void
    {
        static::$inputs[(string)static::currentCommand()] = new ConsoleInput($inputs);
    }

    public static function input(): ConsoleInput
    {
        return static::$inputs[(string)static::currentCommand()] ??= new ConsoleInput(
            new ArgvInput(null)
        );
    }

    public static function setCommand(string|CurrentCommand $command): void
    {
        static::$command = is_string($command) ? new CurrentCommand(0, $command) : $command;
    }

    public static function currentCommand(): CurrentCommand
    {
        return static::$command ??= new CurrentCommand(0, 'default');
    }

    public static function validateEarly(): void
    {
        foreach (static::$validateEarlyArguments[static::currentCommand()->name] ?? [] as $name => $validated) {
            static::getArgument($name);
        }

        foreach (static::$validateEarlyOptions[static::currentCommand()->name] ?? [] as $name => $validated) {
            static::getOption($name);
        }
    }

    public static function resetInput(): void
    {
        static::$inputs = [];
        static::$argumentValidators = [];
        static::$optionValidators = [];
        static::$definitions = [];
    }

    public static function fake(array $parameters = []): void
    {
        static::setInput(
            new ArrayInput($parameters)
        );
    }
}
