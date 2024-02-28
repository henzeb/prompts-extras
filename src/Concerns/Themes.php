<?php

namespace Henzeb\Prompts\Concerns;

use Laravel\Prompts\Prompt as LaravelPrompts;

trait Themes
{
    protected static function setRenderer(
        string $renderer,
        string $theme = 'default'
    ): void
    {
        LaravelPrompts::$themes[$theme ?? 'default'][static::class] = $renderer;
    }
}
