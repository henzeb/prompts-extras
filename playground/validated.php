<?php

use function Henzeb\Prompts\validated;

require __DIR__ . '/../vendor/autoload.php';

$value = 'no';

$confirm = validated(
    value: $value,
    label: 'Please confirm',
    required: true,
    validate: fn($value) => match (true) {
        is_string($value) => $value === 'yes' ? null : 'must be yes.',
        default => null,
    },
    prompt: 'confirm',
    options: [
        'required' => false,
        'default' => false,
        'hint' => 'You must confirm.'
    ],
    submit: true
);

var_dump($confirm);
