<?php

namespace Henzeb\Prompts\Concerns;

use Henzeb\Prompts\Inputs;
use InvalidArgumentException;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputDefinition;

trait InputDefinitions
{
    /**
     * @var array<string, InputDefinition>
     */
    protected static array $definitions = [];

    public static function definition(): InputDefinition
    {
        $command = (string)static::currentCommand();

        return static::$definitions[$command] ??= static::input()->getDefinition();
    }

    public static function setDefinition(InputDefinition $definition): void
    {
        static::$definitions[(string)static::currentCommand()] = $definition;

        if (isset(static::$inputs[(string)static::currentCommand()])) {
            static::$inputs[(string)static::currentCommand()]->setDefinition($definition);
        }
    }

    public static function mergeDefinition(InputDefinition $definition): void
    {
        foreach (static::definition()->getOptions() as $option) {
            $definition->addOption($option);
        }

        foreach (static::definition()->getArguments() as $argument) {
            $definition->addArgument($argument);
        }

        // static::setDefinition($definition);
    }

    protected static function bindDefinition(): void
    {
        try {
            Inputs::input()->bind(static::definition());
        } catch (InvalidOptionException|InvalidArgumentException) {
            // needs to be able to fail to add options and arguments that are flagged
        }
    }
}
