<?php

namespace Henzeb\Prompts\Support\Stacks;

use Stringable;

class CurrentCommand implements Stringable
{
    public function __construct(
        public readonly int    $position,
        public readonly string $name
    )
    {
    }

    public function __toString(): string
    {
        return $this->name.'#'.$this->position;
    }
}
