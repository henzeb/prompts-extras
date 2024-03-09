<?php

namespace Henzeb\Prompts\Concerns;

use Laravel\Prompts\Prompt as LaravelPrompts;

trait Themes
{
    public static function setRenderer(
        string $renderer,
        string $theme = 'default',
        string $prompt = null
    ): void
    {
        LaravelPrompts::$themes[$theme ?? 'default'][$prompt ?? static::class] = $renderer;
    }
}
