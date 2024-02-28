<?php

use Henzeb\Prompts\Inputs;
use Henzeb\Prompts\Prompt;
use Laravel\Prompts\Key;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputOption;
use function Henzeb\Prompts\Input\addOption;
use function Henzeb\Prompts\Input\option;
use function Henzeb\Prompts\Input\optionSet;
use function Henzeb\Prompts\Input\setOption;
use function Henzeb\Prompts\Input\setOptions;
use function Henzeb\Prompts\Input\validateInputEarly;

it('should add option', function () {

    Inputs::fake();

    addOption(
        'test',
        't',
        InputOption::VALUE_REQUIRED,
        'a description',
        'a default',
        ['suggested']
    );

    expect(Inputs::definition()->hasOption('test'))->toBe(true);

    $option = Inputs::definition()->getOption('test');
    expect($option->isValueRequired())->toBeTrue()
        ->and($option->getShortcut())->toBe('t')
        ->and($option->getDescription())->toBe('a description')
        ->and($option->getDefault())->toBe('a default');

    $suggestions = new CompletionSuggestions();

    $option->complete(new CompletionInput(), $suggestions);

    expect($suggestions->getValueSuggestions()[0]->getValue())->toBe('suggested');
});

it('should get option without validation', function () {
    Inputs::fake(['--test' => 'value']);
    addOption('test', InputOption::VALUE_REQUIRED);

    expect(option('test'))->toBe('value');
});

it('should pass option', function () {
    Prompt::fake();
    Inputs::fake(['--test' => 'value']);

    addOption('test', validate: fn($value) => $value !== 'value' ? 'invalid' : null);

    expect(option('test'))->toBe('value');
});

it('should return option twice, validate once', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake(['--test' => 'val']);

    addOption('test',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    expect(option('test'))->toBe('value')
        ->and(option('test'))->toBe('value');

});

it('should not prompt for value', function () {
    Prompt::fake();
    Inputs::fake();

    addOption('test', validate: fn($value) => $value !== 'value' ? 'invalid' : null);

    option('test');
})->expectNotToPerformAssertions();

it('should prompt for no value', function () {
    Prompt::fake(['value', Key::ENTER]);
    Inputs::fake(['--test' => null]);

    addOption('test',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    option('test');
});

it('should prompt for invalid value', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake(['--test' => 'val']);

    addOption('test',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    option('test');
});


it('should prompt for value when required', function () {
    Prompt::fake(['value', Key::ENTER]);
    Inputs::fake();

    addOption('test',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null,
        options: [
            'required' => true
        ]
    );

    expect(option('test'))->toBe('value');
});

it('should validate when changed', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake();

    addOption('test',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    setOption('test', 'val');
});

it('should validate when changed externally', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake();

    addOption('test',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    Inputs::input()->setOption('test', 'val');

    option('test');
});

it('should validate when multiple options', function () {
    Prompt::fake(['lue', Key::ENTER, 'ue', Key::ENTER]);
    Inputs::fake();

    addOption('test',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    addOption('test2',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null
    );

    setOptions([
        'test' => 'va',
        'test2' => 'val'
    ]);
});

it('should validate early', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake(['--test' => 'val']);

    addOption('test',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null,
        validateEarly: true,
    );

    validateInputEarly();
});

it('should validate early and update option', function () {
    Prompt::fake(['ue', Key::ENTER]);
    Inputs::fake(['--test' => 'val']);

    addOption('test',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null,
        validateEarly: true,
    );

    validateInputEarly();

    expect(Inputs::input()->getOption('test'))->toBe('value');
});

it('should not validate while validate early', function () {
    Prompt::fake();
    Inputs::fake(['--test' => 'val']);

    addOption('test',
        mode: InputOption::VALUE_OPTIONAL,
        validate: fn($value) => $value !== 'value' ? 'invalid' : null,
        validateEarly: false,
    );

    validateInputEarly();
})->expectNotToPerformAssertions();

it('should return true when option set', function () {
    Inputs::fake(['--test' => null]);

    addOption('test', InputOption::VALUE_OPTIONAL);

    expect(optionSet('test'))->toBeTrue();
});

it('should return false when option not set', function () {
    Inputs::fake();

    addOption('test', InputOption::VALUE_OPTIONAL);

    expect(optionSet('test'))->toBeFalse();
});




