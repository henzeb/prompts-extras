<?php

namespace Henzeb\Prompts;

use Henzeb\Prompts\Support\Formatter;

/**
 * Continuously updates output on each interval.
 *
 * @param callable(): void $render
 */
function watch(callable $render, ?int $interval = 2): void
{
    (new Watch($render, $interval))->prompt();
}

/**
 * Returns a validated value or prompts for input if value doesn't validate.
 */
function validated(
    mixed           $value,
    string          $label,
    bool|string     $required = false,
    mixed           $validate = null,
    callable|string $prompt = 'text',
    array           $options = [],
    bool            $submit = false,
): mixed
{
    return (new Validated(...func_get_args()))->prompt();
}

function format(string $text, string ...$with): string
{
    return (new Formatter())->render($text, ...$with);
}