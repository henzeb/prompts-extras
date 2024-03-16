<?php

namespace Henzeb\Prompts\Illuminate\Validation;

use function func_get_args;

class Validate
{
    public function __construct(
        public readonly mixed   $rules,
        public readonly array   $messages = [],
        public readonly ?string $attribute = null,
    )
    {
    }

    public static function with(
        mixed  $rules,
        array  $messages = [],
        string $attribute = null,
    ): static
    {
        return new static(...func_get_args());
    }
    
    public function __toString(): string
    {
        return '';
    }
}
