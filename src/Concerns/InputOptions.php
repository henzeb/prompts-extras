<?php

namespace Henzeb\Prompts\Concerns;

use Closure;
use Henzeb\Prompts\Inputs;
use Symfony\Component\Console\Input\InputOption;

use function array_key_exists;
use function Henzeb\Prompts\Input\validateOptionUsing;
use function Henzeb\Prompts\validated;

trait InputOptions
{
    /**
     * @var array<string, array<string, Closure(mixed): mixed>>
     */
    protected static array $optionValidators = [];

    /**
     * @var array<string, array<string, Closure(mixed): mixed>>
     */
    protected static array $validateEarlyOptions = [];

    /**
     * @param string|array<string>|null $shortcut
     * @param int<0, 31>|null $mode
     * @param string|bool|int|float|array<mixed>|null $default
     * @param array<mixed>|Closure $suggestedValues
     * @param array{required?: bool|string, default?: mixed, ...} $options
     */
    public static function addOption(
        string                           $name,
        string|array|null                $shortcut = null,
        ?int                             $mode = null,
        string                           $description = '',
        string|bool|int|float|array|null $default = null,
        array|Closure                    $suggestedValues = [],
        mixed                            $validate = null,
        bool                             $validateEarly = true,
        string|callable|null             $prompt = 'text',
        array                            $options = [],
        bool                             $submit = false,
    ): void {
        Inputs::definition()->addOption(new InputOption($name, $shortcut, $mode, $description, $default, $suggestedValues));

        Inputs::bindDefinition();

        validateOptionUsing($name, $validate, $validateEarly, $prompt ?? 'text', $options, $submit);
    }

    public static function getOption(string $name, mixed $default = null): mixed
    {
        $value = static::input()->getOption($name);

        if (isset(static::$optionValidators[static::currentCommand()->name][$name])) {
            $validated = static::$optionValidators[static::currentCommand()->name][$name]($value);
            if ($validated !== $value) {

                static::input()->setOption($name, $validated);

                return $validated;
            }
        }

        return $value ?? $default;
    }

    public static function setOption(string $name, mixed $value): void
    {
        $command = static::currentCommand()->name;

        static::input()->setOption($name, $value);

        if (isset(static::$optionValidators[$command][$name])) {
            static::$optionValidators[$command][$name]($value);
        }
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function setOptions(array $options): void
    {
        foreach ($options as $argument => $value) {
            static::setOption($argument, $value);
        }
    }

    /**
     * @param array{required?: bool|string, default?: mixed, ...} $options
     */
    public static function validateOptionUsing(
        string          $name,
        mixed           $validate = null,
        bool            $validateEarly = false,
        callable|string $prompt = 'text',
        array           $options = [],
        bool            $submit = true,
    ): void {
        if (!isset($validate) && !isset($options['required'])) {
            return;
        }

        $validated = function (mixed $value) use ($name, $validate, $prompt, $options, $submit) {
            /** @var array<string, array<string, mixed>> $validated */
            static $validated = [];

            $wasValidated = isset($validated[(string)static::currentCommand()][$name]);
            $validatedOption = $validated[(string)static::currentCommand()][$name] ?? null;

            if ($wasValidated && $validatedOption === $value) {
                return $value;
            }

            $option = static::definition()->getOption($name);

            $default = $option->getDefault();

            $options['default'] = array_key_exists('default', $options) ? $options['default'] : ($value ?? $default);

            $isRequired = $options['required'] ?? false;

            $needsValidation = is_string($validate) || is_array($validate) || $isRequired;

            if (!$needsValidation && !static::input()->optionSet($name)) {
                return $value;
            }

            $value = validated($value, $name, $isRequired, $validate, $prompt, $options, $submit);

            return $validated[(string)static::currentCommand()][$name] = $value;
        };

        if ($validateEarly) {
            static::$validateEarlyOptions[static::currentCommand()->name][$name] = $validated;
        }

        static::$optionValidators[static::currentCommand()->name][$name] = $validated;
    }
}
