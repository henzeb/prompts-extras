<?php

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use function Henzeb\Prompts\Input\addArgument;
use function Henzeb\Prompts\Input\addOption;
use function Henzeb\Prompts\Input\argument;
use function Henzeb\Prompts\Input\option;
use function Henzeb\Prompts\Input\optionSet;
use function Henzeb\Prompts\Input\setArgument;
use function Henzeb\Prompts\Input\validateInputEarly;
use function Laravel\Prompts\info;

require __DIR__ . '/../vendor/autoload.php';

addArgument(
    'name',
    mode: InputArgument::OPTIONAL,
    validate: fn($value) => match (true) {
        strlen($value) < 3 => 'The name must be at least 3 characters.',
        strlen($value) > 255 => 'The name must not exceed 255 characters.',
        default => null
    },
    validateEarly: true,
    options: [
        'required' => 'Your name is required.',
        'hint' => 'Type your name e.g.: Taylor.'
    ],
);

addOption(
    'confirm',
    validateEarly: true,
    prompt: 'confirm',
    options: [
        'required' => true,
        'hint' => 'Select yes to continue.'
    ]
);

addOption(
    'optional',
    mode: InputOption::VALUE_OPTIONAL,
    validateEarly: false,
    options: [
        'required' => true,
        'hint' => 'You can put in anything here.'
    ],
    submit: true
);

validateInputEarly();

info('This comes after early validation...');

info('name: ' . argument('name'));

setArgument('name', 'in');

info('confirmed: ' . option('confirm'));

if (optionSet('optional')) {
    info('optional answer: ' . option('optional'));
}





