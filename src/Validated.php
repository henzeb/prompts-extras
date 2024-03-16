<?php

namespace Henzeb\Prompts;

use Closure;
use Laravel\Prompts\Key;
use Laravel\Prompts\MultiSearchPrompt;
use Laravel\Prompts\MultiSelectPrompt;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

class Validated extends Prompt
{
    /**
     * contains prompts which name does not automatically map to an existing interface
     */
    protected static array $registeredPrompts = [
        'multiselect' => MultiSelectPrompt::class,
        'multisearch' => MultiSearchPrompt::class,
    ];
    private string|Closure $prompt = 'text';

    public function __construct(
        protected mixed    $value,
        protected string   $label,
        public bool|string $required = false,
        public mixed       $validate = null,
        string|callable    $prompt = 'text',
        protected array    $options = [],
        protected bool     $submit = false,
    )
    {
        $this->prompt = $this->determineCorrectPrompt($prompt);
    }

    /**
     * @throws ReflectionException
     */
    public function prompt(): mixed
    {
        static::$interactive ??= stream_isatty(STDIN);

        $this->options['label'] ??= $this->label;
        $this->options['validate'] ??= $this->validate;
        $this->options['required'] ??= $this->required;

        $this->submit();

        if ($this->state !== 'submit') {

            if (is_callable($this->prompt)) {
                return ($this->prompt)($this->value);
            }

            $parameters = [];

            static::writeDirectly(Key::ENTER);
            static::moveCursor(0, -1);

            $reflection = new ReflectionMethod($this->prompt, '__construct');

            foreach ($reflection->getParameters() as $parameter) {
                $default = $parameter->isOptional() ? $parameter->getDefaultValue() : null;

                $parameters[$parameter->getName()] = $this->options[$parameter->getName()] ?? $default;

                if ($parameter->getName() === 'required' && $default === true && $this->options[$parameter->getName()] === false) {
                    $parameters[$parameter->getName()] = true;
                }
            }

            if (!is_array($this->value) &&
                isset($parameters['default'])
                && is_array($parameters['default'])
            ) {
                $parameters['default'] = $parameters['default'][0] ?? '';
            }

            if (
                is_array($this->value) && isset($parameters['default'])
                && $this->value === $parameters['default']
                && $this->state === 'error'
            ) {
                foreach ($parameters['default'] as $key => $default) {
                    $this->value = [$default];
                    $this->validated = false;
                    $this->state = 'submit';
                    $this->submit();
                    if ($this->state === 'error') {
                        unset($parameters['default'][$key]);
                    }
                }
                $parameters['default'] = $parameters['default'];
            }

            $prompt = (new ($this->prompt)(...$parameters));

            if ($this->value || $this->submit) {
                $prompt->state = 'error';
                $prompt->error = $this->error;
                $prompt->validated = true;
                $prompt->submit();
            }

            return $prompt->prompt();
        }

        return $this->value();
    }

    public function value(): mixed
    {

        return $this->value;
    }

    private function determineCorrectPrompt(string|callable $prompt): string|Closure
    {
        $namespaces = [
            'Henzeb\\Prompts\\',
            'Laravel\\Prompts\\'
        ];

        if (is_callable($prompt)) {
            return $prompt(...);
        }

        $prompt = static::$registeredPrompts[strtolower($prompt)] ?? $prompt;

        foreach ($namespaces as $namespace) {
            if (class_exists($prompt)) {
                return $prompt;
            }

            if (class_exists($namespace . ucfirst($prompt) . 'Prompt')) {
                return $namespace . ucfirst($prompt) . 'Prompt';
            }
        }

        throw new RuntimeException(
            sprintf(
                'Prompt `%s` does not exist.',
                $this->prompt
            )
        );
    }
}
