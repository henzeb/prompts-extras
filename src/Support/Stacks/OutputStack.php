<?php

namespace Henzeb\Prompts\Support\Stacks;

use Symfony\Component\Console\Output\OutputInterface;

abstract class OutputStack
{
    public static array $outputs = [];

    public static function add(OutputInterface $command): void
    {
        self::$outputs[] = $command;
    }

    public static function pop(): ?OutputInterface
    {
        $current = array_pop(self::$outputs);

        return self::current() ?: $current;

    }

    public static function hasOutputs(): bool
    {
        return !empty(self::$outputs);
    }

    public static function current(): ?OutputInterface
    {
        return end(self::$outputs) ?: null;
    }
}
