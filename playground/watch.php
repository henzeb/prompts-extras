<?php


use Laravel\Prompts\Prompt;
use function Henzeb\Prompts\watch;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

require __DIR__ . '/../vendor/autoload.php';


Prompt::cancelUsing(
    function () {
        info('bye...');
    }
);

watch(
    render: function () {
        static $iteration = 0;
        static $items = [];

        if (count($items) === 5) {
            array_shift($items);
        }

        $items[] = [$iteration += 1, (new Datetime())->format(DateTime::RFC850)];

        if (count($items) === 5) {
            info(sprintf('Now the table just scrolls, %s and counting...', $iteration));
        } else {
            info('Filling up the table...');
        }

        progress('a nice progressbar', 5)->advance($iteration % 5);

        $ralph = text('Ralph', default: 'Ralph Wiggum: I\'m ignored!');

        note($ralph);

        table(
            [
                'Iteration',
                'DateTime'
            ],
            $items
        );
    },
    interval: 1,
);
