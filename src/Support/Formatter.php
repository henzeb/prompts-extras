<?php

namespace Henzeb\Prompts\Support;

use Laravel\Prompts\Concerns\Colors;

class Formatter
{
    use Colors;

    public function render(string $text, string ...$formats): string
    {
        foreach ($formats as $format) {
            $text = $this->$format($text);
        }

        return $text;
    }
}