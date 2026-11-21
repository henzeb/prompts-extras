<?php

namespace Henzeb\Prompts\Concerns;

use Laravel\Prompts\Prompt as LaravelPrompts;

trait Themes
{
    /**
     * @param class-string<callable-object> $renderer
     * @param class-string<LaravelPrompts>|null $prompt
     */
    public static function setRenderer(
        string $renderer,
        string $theme = 'default',
        ?string $prompt = null
    ): void {
        LaravelPrompts::$themes[$theme][$prompt ?? static::class] = $renderer;
    }
}
