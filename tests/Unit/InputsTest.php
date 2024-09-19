<?php

use Henzeb\Prompts\Inputs;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use function Henzeb\Prompts\Input\addArgument;
use function Henzeb\Prompts\Input\addOption;

it('should return definition from input', function () {
    $definition = new InputDefinition();
    $input = new ArrayInput([], $definition);

    Inputs::setInput($input);

    expect(Inputs::definition())->toBe($definition);
});

it('should not throw exceptions when binding option input', function () {
    Inputs::fake(['--test' => true]);

    addOption('confirm');

})->throwsNoExceptions();

it('should not throw exceptions when binding argument input', function () {
    Inputs::fake(['test' => true]);

    addArgument('confirm');

})->throwsNoExceptions();
