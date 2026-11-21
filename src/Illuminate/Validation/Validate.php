<?php

namespace Henzeb\Prompts\Illuminate\Validation;

/**
 * @phpstan-consistent-constructor
 */
class Validate
{
    /**
     * @param array<string, string> $messages
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly mixed   $rules,
        public readonly array   $messages = [],
        public readonly ?string $attribute = null,
        public readonly array   $data = [],
    ) {
    }

    /**
     * @param array<string, string> $messages
     * @param array<string, mixed> $data
     */
    public static function with(
        mixed  $rules,
        array  $messages = [],
        ?string $attribute = null,
        array  $data = [],
    ): static {
        return new static($rules, $messages, $attribute, $data);
    }

    public function __toString(): string
    {
        return '';
    }
}
