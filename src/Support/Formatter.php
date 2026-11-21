<?php

namespace Henzeb\Prompts\Support;

use Laravel\Prompts\Concerns\Colors;

class Formatter
{
    use Colors;

    public function render(string $text, string ...$formats): string
    {
        foreach ($formats as $format) {
            $formatted = $this->$format($text);

            $text = is_string($formatted) ? $formatted : $text;
        }

        return $text;
    }
}
