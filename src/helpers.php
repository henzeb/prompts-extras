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
