<?php

namespace Henzeb\Prompts;

use Laravel\Prompts\Prompt as LaravelPrompt;
use Laravel\Prompts\Themes\Default\Renderer;
use Mockery\MockInterface;
use Throwable;

abstract class Prompt extends LaravelPrompt
{
    protected static function setRenderer(
        string      $renderer,
        string        $theme = 'default'
    ): void {
        static::$themes[$theme ?? 'default'][static::class] = $renderer;
    }
    protected static function isFaked(): bool
    {
        return static::terminal() instanceof MockInterface;
    }
}
