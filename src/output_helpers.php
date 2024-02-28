<?php

namespace Henzeb\Prompts;

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
