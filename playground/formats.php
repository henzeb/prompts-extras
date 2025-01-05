<?php


use function Henzeb\Prompts\format;
use function Laravel\Prompts\table;

require __DIR__ . '/../vendor/autoload.php';

echo format('this is red', 'red') . PHP_EOL;

echo format('this is strikethrough', 'strikethrough') . PHP_EOL;

echo format(
        'this is a bold black text with cyan background',
        'black',
        'bold',
        'bgCyan',
    ) . PHP_EOL;

table(
    [
        format('animals', 'bold', 'green')
    ],
    [
        [
            format(
                'cats',
                'green',
                'bold'
            )
        ],
        [
            format(
                'dogs',
                'red',
                'bold',
                'strikethrough'
            )
        ]
    ]
);
