<?php

namespace Henzeb\Prompts\Illuminate\Validation;

use Henzeb\Prompts\Inputs;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator as LaravelValidator;
use Illuminate\Validation\ConditionalRules;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt as LaravelPrompt;
use Ramsey\Uuid\Uuid;
use WeakMap;
use function collect;
use function Henzeb\Prompts\Input\argumentSet;
use function Henzeb\Prompts\Input\optionSet;
use function is_array;
use function is_string;
use function trans;

class Validator
{
    protected WeakMap $input;

    public function __construct()
    {
        $this->input = new WeakMap();
    }

    public function __invoke(LaravelPrompt $prompt): ?string
    {
        $rules = $prompt->validate;
        $messages = [];
        $attribute = trans('prompts/extras::prompts.attribute');

        if ($prompt->validate instanceof Validate) {
            $rules = $prompt->validate->rules;
            $messages = $prompt->validate->messages;
            $attribute = $prompt->validate->attribute ?? $attribute;
        }

        if (!$rules
            || !(is_string($rules)
                || is_array($rules)
                || $rules instanceof ConditionalRules
            )
        ) {
            return null;
        }

        $rulesName = $name = (string)Uuid::uuid4();

        if (is_array($prompt->value())) {
            $rulesName .= '.*';
        }

        $errors = LaravelValidator::make(
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
        )->join(Key::ENTER . '  ⚠ ');
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
        return $messages + Lang::get('prompts/extras::validation');
    }
}
