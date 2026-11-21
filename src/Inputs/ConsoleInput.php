<?php

namespace Henzeb\Prompts\Inputs;

use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputDefinition;

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

    /**
     * @param string|array<string> $values
     */
    public function hasParameterOption(array|string $values, bool $onlyParams = false): bool
    {
        return $this->input->hasParameterOption($values, $onlyParams);
    }

    /**
     * @param string|array<string> $values
     * @param string|bool|int|float|array<mixed>|null $default
     */
    public function getParameterOption(array|string $values, float|array|bool|int|string|null $default = false, bool $onlyParams = false): mixed
    {
        return $this->input->getParameterOption($values, $default, $onlyParams);
    }

    /**
     * @param array<mixed> $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->input->$name(...$arguments);
    }

    public function __toString(): string
    {
        return (string)$this->input;
    }
}
