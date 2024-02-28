<?php

use Henzeb\Prompts\Inputs\ConsoleInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;


it('should return definition from input', function () {
    $definition = new InputDefinition();
    $input = new ArrayInput([], $definition);

    expect((new ConsoleInput($input))->getDefinition())->toBe($definition);
});

it('should return optionSet is false from input', function () {
    $definition = new InputDefinition();
    $definition->addOption(new InputOption('test'));
    $input = new ArrayInput([], $definition);

    expect((new ConsoleInput($input))->optionSet('test'))->toBeFalse();
});

it('should return optionSet is true from input', function () {
    $definition = new InputDefinition();
    $definition->addOption(new InputOption('test'));
    $input = new ArrayInput(['--test' => 'set'], $definition);

    expect((new ConsoleInput($input))->optionSet('test'))->toBeTrue();
});

it('should return argumentSet is false from input', function () {
    $definition = new InputDefinition();
    $definition->addArgument(new InputArgument('test'));
    $input = new ArrayInput([], $definition);

    expect((new ConsoleInput($input))->argumentSet('test'))->toBeFalse();
});

it('should return argumentSet is true from input', function () {
    $definition = new InputDefinition();
    $definition->addArgument(new InputArgument('test'));
    $input = new ArrayInput(['test' => 'set'], $definition);

    expect((new ConsoleInput($input))->argumentSet('test'))->toBeTrue();
});

it('should return first argument from input', function () {
    $definition = new InputDefinition();
    $definition->addArgument(new InputArgument('test'));
    $definition->addArgument(new InputArgument('another'));
    $input = new ArrayInput(['test' => 'set', 'another' => 'another set'], $definition);

    expect((new ConsoleInput($input))->getFirstArgument())->toBe('set');
});

it('should return true from hasParameterOption', function(){
    $mock = Mockery::mock(Input::class);
    $mock->expects('hasParameterOption')->with('test', true)
        ->andReturn(true)
        ->once();

    $value = (new ConsoleInput($mock))->hasParameterOption('test', true);

    expect($value)->toBe(true);
});

it('should return value from getParameterOption', function(){
    $mock = Mockery::mock(Input::class);
    $mock->expects('getParameterOption')->with('test', 'default', true)
        ->andReturn('your value')
        ->once();

    $value = (new ConsoleInput($mock))->getParameterOption('test', 'default', true);

    expect($value)->toBe('your value');

});

it('should interact with input through __call', function(){
    $mock = Mockery::mock(Input::class);
    $mock->expects('doesNotExist')->with('name')
        ->andReturn('your value')
        ->once();

    $value = (new ConsoleInput($mock))->doesNotExist('name');

    expect($value)->toBe('your value');

});
