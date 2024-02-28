<?php

namespace Henzeb\Prompts\Inputs;

use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @method string __toString()
 */
class ConsoleInput extends Input
{
    public function __construct(private readonly Input $input)
    {
        parent::__construct();

        $this->definition = &$this->input->definition;
        $this->options = &$this->input->options;
        $this->arguments = &$this->input->arguments;
    }

    public function getDefinition(): InputDefinition
    {
        return $this->input->definition;
    }

    public function optionSet(string $name): bool
    {
        return array_key_exists($name, $this->input->options);
    }

    public function argumentSet(string $name): bool
    {
        return array_key_exists($name, $this->input->arguments);
    }

    public function setDefinition(InputDefinition $definition): void
    {
        $this->input->bind($definition);
    }

    protected function parse(): void
    {
       // This is unused as this is a proxy
        $this->input->parse();
    }

    public function getFirstArgument(): ?string
    {
        return $this->input->getFirstArgument();
    }

    public function hasParameterOption(array|string $values, bool $onlyParams = false): bool
    {
        return $this->input->hasParameterOption($values, $onlyParams);
    }

    public function getParameterOption(array|string $values, float|array|bool|int|string|null $default = false, bool $onlyParams = false)
    {
        return $this->input->getParameterOption($values, $default, $onlyParams);
    }

    public function __call(string $name, array $arguments)
    {
        return $this->input->$name(...$arguments);
    }
}
