<?php

namespace Henzeb\Prompts\Concerns;

use Laravel\Prompts\Prompt as LaravelPrompt;
use Mockery\MockInterface;

trait FakesInputOutput
{
    protected static function isFaked(): bool
    {
        return LaravelPrompt::terminal() instanceof MockInterface;
    }
}
