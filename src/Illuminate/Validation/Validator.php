<?php

namespace Henzeb\Prompts\Illuminate\Validation;

use Henzeb\Prompts\Inputs;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator as LaravelValidator;
use Illuminate\Translation\CreatesPotentiallyTranslatedStrings;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ConditionalRules;
use Illuminate\Validation\Factory;
use Illuminate\Validation\NestedRules;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt as LaravelPrompt;
use WeakMap;
use function collect;
use function Henzeb\Prompts\Input\argumentSet;
use function Henzeb\Prompts\Input\optionSet;
use function is_array;
use function is_string;
use function str_replace;
use function uniqid;


class Validator
{
    use CreatesPotentiallyTranslatedStrings;

    public const NAMESPACE = 'prompts/extras::';

    protected WeakMap $input;
    protected Translator $translator;

    public function __construct(
        private ?Factory $factory = null
    )
    {
        $this->factory ??= LaravelValidator::getFacadeRoot();
        $this->translator = $this->factory->getTranslator();
        $this->input = new WeakMap();
    }

    public function __invoke(LaravelPrompt $prompt): ?string
    {
        $rules = $prompt->validate;
        $messages = [];
        $data = [];
        $attribute = $this->translator->get(static::NAMESPACE . 'prompts.attribute');

        if ($prompt->validate instanceof Validate) {
            $rules = $prompt->validate->rules;
            $messages = $prompt->validate->messages;
            $data = $prompt->validate->data;
            $attribute = $prompt->validate->attribute ?? $attribute;
        }

        if (!$rules
            || !(is_string($rules)
                || is_array($rules)
                || $rules instanceof ConditionalRules
                || $rules instanceof NestedRules
                || $rules instanceof ValidationRule
            )
        ) {
            return null;
        }

        $rulesName = $name = str_replace(
            '.',
            '',
            uniqid(prefix: 'field', more_entropy: true)
        );

        if (is_array($prompt->value())) {
            $rulesName .= '.*';
        }

        $errors = $this->factory->make(
            $data +
            $this->getInput($prompt)
            + [$name => $prompt->value()],
            [$rulesName => $rules],
            $this->getMessages($messages),
            [
                $name => $attribute
            ]
        )->getMessageBag();

        if ($errors->isEmpty()) {
            return null;
        }

        return collect(
            $errors->all()
        )->map(fn(string $error) => $error . ' ')
            ->join(Key::ENTER . '  ⚠ ');
    }

    protected function getInput(LaravelPrompt $prompt): array
    {
        if (isset($this->input[$prompt])) {
            return $this->input[$prompt];
        }

        $this->input[$prompt] = [];

        foreach (Inputs::definition()->getArguments() as $argument) {
            if (argumentSet($argument->getName()) || null !== $argument->getDefault()) {
                $this->input[$prompt][$argument->getName()] = Inputs::input()->getArgument($argument->getName());
            }
        }

        foreach (Inputs::definition()->getOptions() as $option) {
            if (optionSet($option->getName()) || null !== $option->getDefault()) {
                $this->input[$prompt]['--' . $option->getName()] = Inputs::input()->getOption($option->getName());
            }
        }

        return $this->input[$prompt];
    }

    private function getMessages(array $messages): array
    {
        return $messages + (array)$this->translator->get(static::NAMESPACE . 'validation');
    }
}
