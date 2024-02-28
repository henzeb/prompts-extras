<?php

use Henzeb\Prompts\Prompt;
use Laravel\Prompts\Key;
use Laravel\Prompts\PasswordPrompt;
use function Henzeb\Prompts\validated;
use function Laravel\Prompts\confirm;

it('should return valid value', function () {
    $value = validated('value', 'my label', required: true);

    expect($value)->toBe('value');
});

it('should prompt for required value', function () {
    Prompt::fake([Key::ENTER, 'value', Key::ENTER]);
    $value = validated(null, 'my label', required: true);

    Prompt::assertOutputContains('my label');

    expect($value)->toBe('value');
});

it('should prompt for invalid value ', function () {
    Prompt::fake([Key::ENTER, 'value', Key::ENTER]);
    $value = validated(null, 'my own label', validate: fn($value) => $value ? null : 'fails');

    Prompt::assertOutputContains('my own label');

    expect($value)->toBe('value');
});

it('should use different prompt for required value ', function () {
    Prompt::fake([Key::ENTER, 'value', Key::ENTER]);
    $value = validated(null, 'password?', required: true, prompt: 'password');

    Prompt::assertOutputContains('password?');

    Prompt::assertOutputContains('•••••');

    expect($value)->toBe('value');
});

it('should use FCQN prompt for required value ', function () {
    Prompt::fake([Key::ENTER, 'value', Key::ENTER]);
    $value = validated(null, 'password?', required: true, prompt: PasswordPrompt::class);

    Prompt::assertOutputContains('password');

    Prompt::assertOutputContains('•••••');

    expect($value)->toBe('value');
});

it('should not show required when submit is false and value null', function () {
    Prompt::fake(['value', Key::CTRL_C]);
    $value = validated(null, 'label', required: true, submit: false);

    Prompt::assertOutputDoesntContain('Required.');
});

it('should show required when submit is true and value null', function () {
    Prompt::fake([Key::CTRL_C]);

    validated(null, 'label', required: true, submit: true);

    Prompt::assertOutputContains('Required.');
});

it('should not show validation error when submit is false and value is null', function () {
    Prompt::fake([Key::CTRL_C]);
    validated(null, 'label', validate: fn() => 'validation error.', submit: false);

    Prompt::assertOutputDoesntContain('validation error.');
});

it('should not show validation error when submit is false and value is not null', function () {
    Prompt::fake([Key::CTRL_C]);
    validated('val', 'label', validate: fn() => 'validation error.', submit: false);

    Prompt::assertOutputContains('validation error.');
});

it('should throw error when prompt does not exist', function () {
    validated('value', 'fake', prompt: 'doesNotExist');
})->expectException(RuntimeException::class);

it('should show validation error when submit is true and value null', function () {
    Prompt::fake([Key::CTRL_C]);
    validated(null, 'label', validate: fn() => 'validation error.', submit: true);

    Prompt::assertOutputContains('validation error.');
});

it('should override options with label', function () {
    Prompt::fake([Key::CTRL_C]);

    validated(null, 'label', required: true, options: ['label' => 'Name']);

    Prompt::assertOutputContains('Name');

    Prompt::assertOutputDoesntContain('label');
});

it('should override options with required', function () {
    Prompt::fake([Key::ENTER]);

    validated(null, 'label', required: true, options: ['required' => false]);

});

it('should override options with validate', function () {
    Prompt::fake([Key::ENTER]);

    validated(null, 'label', validate: fn() => 'failure', options: ['validate' => fn() => null]);

});

it('should override options with hint', function () {
    Prompt::fake([Key::CTRL_C]);

    validated(null, 'label', required: true, options: ['hint' => 'this is a hint']);

    Prompt::assertOutputContains('this is a hint');
});

it('should use empty string as default when tried with array', function () {
    Prompt::fake([Key::CTRL_C]);

    validated(null, 'label', required: true, options: ['default' => []]);
});


it('should use first item as default when tried with array', function () {
    Prompt::fake([Key::CTRL_C]);

    validated(null, 'label', required: true, options: ['default' => ['a default']]);

    Prompt::assertOutputContains('a default');
});

it('should accept callables for prompt', function () {
    Prompt::fake([Key::CTRL_C]);
    validated(null, 'label', required: true, prompt: fn() => confirm('my label'));

    Prompt::assertOutputContains('my label');
    Prompt::assertOutputContains('Yes');
    Prompt::assertOutputContains('No');
});

it('should set required to default true when prompt is select', function () {

    Prompt::fake([Key::CTRL_C]);

    validated(
        null,
        label: 'test',
        required: true,
        prompt: 'select',
        options: [
            'required' => false,
            'options' => ['a', 'b']
        ]
    );
});
