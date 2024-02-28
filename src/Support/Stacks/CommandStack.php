<?php

namespace Henzeb\Prompts\Support\Stacks;

abstract class CommandStack
{
    public static array $commands = [];

    public static function add(string $command): void
    {
        self::$commands[] = $command;
    }

    public static function pop(): ?CurrentCommand
    {
        array_pop(self::$commands);

        return static::current();
    }

    public static function hasCommands(): bool
    {
        return !empty(static::$commands);
    }

    public static function current(): CurrentCommand
    {
        return new CurrentCommand(
            count(static::$commands),
            end(static::$commands)
        );
    }
}
