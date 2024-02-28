<?php

use Henzeb\Prompts\Inputs;
use Henzeb\Prompts\Prompt;
use Laravel\Prompts\Key;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputArgument;
use function Henzeb\Prompts\Input\addArgument;
use function Henzeb\Prompts\Input\Argument;
use function Henzeb\Prompts\Input\argumentSet;
use function Henzeb\Prompts\Input\optionSet;
use function Henzeb\Prompts\Input\setArgument;
use function Henzeb\Prompts\Input\setArguments;
use function Henzeb\Prompts\Input\validateInputEarly;

it('should add argument', function () {

    Inputs::fake();

    addArgument(
        'test',
        InputArgument::OPTIONAL,
        'a description',
        'a default',
        ['suggested']
    );

    expect(Inputs::definition()->hasArgument('test'))->toBe(true);

    $option = Inputs::definition()->getArgument('test');
    expect($option->isRequired())->toBeFalse()
        ->and($option->getDescription())->toBe('a description')
        ->and($option->getDefault())->toBe('a default');

    $suggestions = new CompletionSuggestions();

    $option->complete(new CompletionInput(), $suggestions);

    expect($suggestions->getValueSuggestions()[0]->getValue())->toBe('suggested');
});

it('should get argument without validation', function () {
    Inputs::fake(['test' => 'value']);

    addArgument('test', InputArgument::REQUIRED);

    expect(argument('test'))->toBe('value');
});

it('should pass argument', function () {
    Prompt::fake();
    Inputs::fake(['test' => 'value']);

    addArgument('test', validate: fn($value) => $value !== 'value' ? 'invalid' : null);

    expect(argument('test'))->toBe('value');
});

it('should return argument twice, validate once', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake(['test' => 'val']);

    addArgument('test',
        mode: InputArgument::OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    expect(argument('test'))->toBe('value')
        ->and(argument('test'))->toBe('value');

});

it('should prompt for value', function () {
    Prompt::fake(['value', Key::ENTER]);
    Inputs::fake();

    addArgument('test', validate: fn($value) => $value !== 'value' ? 'invalid' : null);

    argument('test');
});

it('should prompt for no value', function () {
    Prompt::fake(['value', Key::ENTER]);
    Inputs::fake(['test' => null]);

    addArgument(
        'test',
        mode: InputArgument::OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    argument('test');
});

it('should prompt for invalid value', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake(['test' => 'val']);

    addArgument('test',
        mode: InputArgument::OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    argument('test');
});

it('should validate when changed', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake();

    addArgument('test',
        mode: InputArgument::OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    setArgument('test', 'val');
});

it('should validate when changed externally', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake();

    addArgument('test',
        mode: InputArgument::OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    Inputs::input()->setArgument('test', 'val');

    argument('test');
});

it('should validate when multiple arguments', function () {
    Prompt::fake(['lue', Key::ENTER, 'ue', Key::ENTER]);
    Inputs::fake();

    addArgument('test',
        mode: InputArgument::OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    addArgument('test2',
        mode: InputArgument::OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    setArguments([
        'test' => 'va',
        'test2' => 'val'
    ]);
});

it('should validate early', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake(['test' => 'val']);

    addArgument('test',
        mode: InputArgument::OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null,
        validateEarly: true,
    );

    validateInputEarly();
});

it('should validate early and update argument', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake(['test' => 'val']);

    addArgument('test',
        mode: InputArgument::OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null,
        validateEarly: true,
    );

    validateInputEarly();

    expect(Inputs::input()->getArgument('test'))->toBe('value');
});

it('should not validate while validate early', function () {
    Prompt::fake();
    Inputs::fake(['test' => 'val']);

    addArgument('test',
        mode: InputArgument::OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null,
        validateEarly: false,
    );

    validateInputEarly();
})->expectNotToPerformAssertions();

it('should return true when argument set', function () {
    Inputs::fake(['test' => null]);

    addArgument('test', InputArgument::OPTIONAL);

    expect(argumentSet('test'))->toBeTrue();
});

it('should return false when argument not set', function () {
    Inputs::fake();

    addArgument('test', InputArgument::OPTIONAL);

    expect(optionSet('test'))->toBeFalse();
});




