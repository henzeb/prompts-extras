#!/usr/bin/env php
<?php

use Henzeb\Prompts\Illuminate\Validation\Validate;
use Henzeb\Prompts\Support\Formatter;
use Illuminate\Support\Str;
use function Henzeb\Prompts\Input\addArgument;
use function Henzeb\Prompts\Input\argument;

require __DIR__ . '/vendor/autoload.php';

addArgument(
    'path',
    default: './ide.json'
);

$functions = collect(get_defined_functions()['user'])
    ->filter(
        fn($str) => str_starts_with($str, 'laravel\prompts')
            || str_starts_with($str, 'henzeb\prompts')
    )->map(fn($function) => (new ReflectionFunction($function))->getName());

$validatableFunctions = $functions->filter(
    fn($str) => count(
        array_filter(
            (new ReflectionFunction($str))->getParameters(),
            fn(ReflectionParameter $parameter) => $parameter->name === 'validate')
    )
);

$promptFunctions = $validatableFunctions->filter(function ($str) {
    if (str_ends_with($str, 'validated')) {
        return false;
    }

    $returnType = (new ReflectionFunction($str))->getReturnType();

    return $returnType instanceof ReflectionUnionType
        || $returnType->getName() !== 'void';
});

$prompts = $promptFunctions->map(
    fn($str) => Str::afterLast($str, '\\')
);

$promptOptions = $promptFunctions
    ->map(
        function ($function) {
            $parameters = (new ReflectionFunction($function))->getParameters();
            return array_map(
                fn(ReflectionParameter $parameter) => $parameter->getName(),
                $parameters
            );
        }
    )->flatten()
    ->unique();

$promptableFunctions = $functions->filter(
    fn($str) => count(
        array_filter(
            (new ReflectionFunction($str))->getParameters(),
            fn(ReflectionParameter $parameter) => $parameter->name === 'prompt')
    )
);

$formatOptions = collect((new ReflectionClass(Formatter::class))->getTraits())
    ->map(function (ReflectionClass $trait) {
        return collect($trait->getMethods(ReflectionMethod::IS_PUBLIC))->map(
            function (ReflectionMethod $method) {
                return $method->getName();
            }
        );
    })->flatten();

$ideJson = [
    '$schema' => 'https://laravel-ide.com/schema/laravel-ide-v2.json',
    'completions' => [
        [
            'complete' => 'validationRules',
            'condition' => [
                [
                    'functionFqn' => $validatableFunctions->values(),
                    'parameterNames' => ['validate'],
                ],
                [
                    'methodNames' => ["with"],
                    "parameters" => [1]
                ]
            ]
        ],
        [
            'complete' => 'validationRule',
            'condition' => [
                [
                    'functionFqn' => $validatableFunctions->values(),
                    'parameterNames' => ['validate'],
                    'place' => 'arrayValue',
                ],
                [
                    'classFqn' => [
                        Validate::class
                    ],
                    'methodNames' => ["with"],
                    'place' => 'arrayValue',
                    "parameters" => [1]
                ]
            ]
        ],
        [
            'complete' => 'staticStrings',
            'options' => [
                'strings' => $prompts->values()
            ],
            'condition' => [
                [
                    'classFqn' => [
                        Validate::class
                    ],
                    'functionFqn' => $promptableFunctions->values(),
                    'parameterNames' => ['prompt'],
                ]
            ]
        ],
        [
            'complete' => 'staticStrings',
            'options' => [
                'strings' => $promptOptions->values()
            ],

            'condition' => [
                [
                    'functionFqn' => $promptableFunctions->values(),
                    'parameterNames' => ['options'],
                    'place' => 'arrayKey',
                ]
            ]
        ],
        [
            'complete' => 'staticStrings',
            'options' => [
                'strings' => $formatOptions->values()
            ],

            'condition' => [
                [
                    'functionFqn' => ['Henzeb\\Prompts\\format'],
                    //                   'parameterNames' => ['with'],
                    'place' => 'parameter',
                ]
            ]
        ]
    ]
];

$ideJson = str_replace('\/', '/', json_encode($ideJson, JSON_PRETTY_PRINT));

file_put_contents(
    argument('path'),
    $ideJson
);
