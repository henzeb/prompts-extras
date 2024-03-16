<?php

namespace Henzeb\Prompts\Concerns;

use Closure;
use Henzeb\Prompts\Inputs;
use Symfony\Component\Console\Input\InputArgument;
use function array_key_exists;
use function Henzeb\Prompts\Input\validateArgumentUsing;
use function Henzeb\Prompts\validated;

trait InputArguments
{
    protected static array $argumentValidators = [];

    protected static array $validateEarlyArguments = [];

    public static function addArgument(
        string                           $name,
        ?int                             $mode = null,
        string                           $description = '',
        string|bool|int|float|array|null $default = null,
        Closure|array                    $suggestedValues = [],
        mixed                            $validate = null,
        bool                             $validateEarly = false,
        string|callable|null             $prompt = 'text',
        array                            $options = [],
        bool                             $submit = false,
    ): void
    {
        $args = func_get_args();

        Inputs::definition()->addArgument(new InputArgument(...$args));

        Inputs::bindDefinition();

        validateArgumentUsing($name, $validate, $validateEarly, $prompt, $options, $submit);
    }

    public static function setArgument(string $name, mixed $value): void
    {

        $command = static::currentCommand()->name;

        static::input()->setArgument($name, $value);

        if (isset(static::$argumentValidators[$command][$name])) {
            static::$argumentValidators[$command][$name]($value);
        }
    }


    public static function setArguments(array $arguments): void
    {
        foreach ($arguments as $argument => $value) {
            static::setArgument($argument, $value);
        }
    }

    public static function getArgument(string $name, mixed $default = null): mixed
    {
        $value = static::input()->getArgument($name);

        if (isset(static::$argumentValidators[static::currentCommand()->name][$name])) {
            $validated = static::$argumentValidators[static::currentCommand()->name][$name]($value);
            if ($validated !== $value) {

                static::input()->setArgument($name, $validated);

                return $validated;
            }
        }

        return $value ?? $default;
    }

    public static function validateArgumentUsing(
        string  $name,
        mixed   $validate = null,
        bool    $validateEarly = false,
        ?string $prompt = 'text',
        array   $options = [],
        bool    $submit = false,
    ): void
    {
        $validated = function (mixed $value)
        use (
            $name,
            $validate,
            $prompt,
            $options,
            $submit
        ) {
            static $validated = [];

            $wasValidated = isset($validated[(string)static::currentCommand()][$name]);
            $validatedArgument = $validated[(string)static::currentCommand()][$name] ?? null;

            if ($wasValidated && $validatedArgument === $value) {
                return $value;
            }

            $argument = static::definition()->getArgument($name);

            $default = $argument->getDefault();

            $required = $argument->isRequired();

            $options['default'] = array_key_exists('default', $options) ? $options['default'] : ($value ?? $default);

            $options['required'] = array_key_exists('required', $options) ? $options['required'] : $required;

            $value = validated($value, $name, $options['required'], $validate, $prompt, $options, $submit);

            return $validated[(string)static::currentCommand()][$name] = $value;
        };

        if ($validateEarly) {
            static::$validateEarlyArguments[static::currentCommand()->name][$name] = $validated;
        }

        static::$argumentValidators[static::currentCommand()->name][$name] = $validated;
    }
}
