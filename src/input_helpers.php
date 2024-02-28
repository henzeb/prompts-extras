<?php

namespace Henzeb\Prompts\Input;

use Closure;
use Henzeb\Prompts\Inputs;

/**
 * Validates commandline arguments and options
 */
function validateInputEarly(): void
{
    Inputs::validateEarly();
}

/**
 * Adds an argument to the definition.
 */
function addArgument(
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
    Inputs::addArgument(...func_get_args());
}

/**
 * Adds an option to the definition.
 */
function addOption(
    string                           $name,
    string|array|null                $shortcut = null,
    ?int                             $mode = null,
    string                           $description = '',
    string|bool|int|float|array|null $default = null,
    array|Closure                    $suggestedValues = [],
    mixed                            $validate = null,
    bool                             $validateEarly = false,
    string|callable|null             $prompt = 'text',
    array                            $options = [],
    bool                             $submit = false,
): void
{
    Inputs::addOption(...func_get_args());
}

/**
 * Retrieves a value of an argument. Prompts if value doesn't validate.
 */
function argument(string $name, mixed $default = null): mixed
{
    return Inputs::getArgument($name, $default);
}

/**
 * Retrieves a value of an option. Prompts if value doesn't validate.
 */
function option(string $name, mixed $default = null): mixed
{
    return Inputs::getOption($name, $default);
}

/**
 * Retrieves a value of an argument. Prompts if value doesn't validate.
 */
function optionSet(string $name): bool
{
    return Inputs::input()->optionSet($name);
}

/**
 * Tests if an argument is actually passed in the commandline.
 */
function argumentSet(string $name): bool
{
    return Inputs::input()->argumentSet($name);
}

/**
 * Sets a new value for an argument. Prompts if it doesn't validate.
 */
function setArgument(string $name, mixed $value): void
{
    Inputs::setArgument($name, $value);
}

/**
 * Sets a new value for an option. Prompts if it doesn't validate.
 */
function setOption(string $name, mixed $value): void
{
    Inputs::setOption($name, $value);
}

/**
 * Sets array of arguments. Prompts on each invalid argument.
 *
 * @param array<string,mixed> $arguments
 */
function setArguments(array $arguments): void
{
    Inputs::setArguments($arguments);
}

/**
 * Sets array of options. Prompts on each invalid argument.
 *
 * @param array<string,mixed> $options
 */
function setOptions(array $options): void
{
    Inputs::setOptions($options);
}

/**
 * @param string $name
 * @param mixed|null $validate
 * @param bool $validateEarly
 * @param string|callable $prompt
 * @param array $options
 * @param bool $submit
 * @return void
 */
function validateOptionUsing(
    string          $name,
    mixed           $validate = null,
    bool            $validateEarly = false,
    string|callable $prompt = 'text',
    array           $options = [],
    bool            $submit = false
): void
{
    Inputs::validateOptionUsing(
        ...func_get_args()
    );
}

function validateArgumentUsing(
    string               $name,
    mixed                $validate = null,
    bool                 $validateEarly = false,
    string|callable|null $prompt = 'text',
    array                $options = [],
    bool                 $submit = false
): void
{
    Inputs::validateArgumentUsing(
        ...func_get_args()
    );
}
