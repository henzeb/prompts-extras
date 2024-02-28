<?php

use Henzeb\Prompts\Support\Stacks\CurrentCommand;

it('should return the name', function () {
    $command = new CurrentCommand(0, 'test');

    expect($command->name)->toBe('test');
});

it('should return the position', function () {
    $command = new CurrentCommand(1, 'test');

    expect($command->position)->toBe(1);
});

it('should cast to string with a unique id', function () {
    $command = new CurrentCommand(1, 'test');

    expect((string)$command)->toBe('test#1');
});
