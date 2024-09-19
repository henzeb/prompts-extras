<?php

use Henzeb\Prompts\Prompt;
use Illuminate\Console\BufferedConsoleOutput;
use Illuminate\Console\OutputStyle;
use Laravel\Prompts\Output\BufferedConsoleOutput as PromptsBufferedConsoleOutput;
use Laravel\Prompts\Output\ConsoleOutput;
use Laravel\Prompts\Prompt as LaravelPrompt;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

it('Prompts do not have a value by default.', function () {
    (new class extends Prompt {

    })->value();
})->throws(RuntimeException::class);

it('isFaked should return true when faked', function () {
    LaravelPrompt::fake();

    $prompt = new class extends Prompt {
        public static function isFaked(): bool
        {
            return parent::isFaked();
        }
    };

    expect($prompt::isFaked())->toBeTrue();
});

it('isFaked should return false when not faked', function () {
    $prompt = new class extends Prompt {
        public static function isFaked(): bool
        {
            return parent::isFaked();
        }
    };

    expect($prompt::isFaked())->toBeFalse();
});

it('updateInteractiveMode should update interactive', function (
    OutputInterface $output,
    bool            $expected
) {
    LaravelPrompt::interactive();
    LaravelPrompt::setOutput($output);
    Prompt::updateInteractivity();

    expect(Prompt::interactivityEnabled())->toBe($expected);
})->with([
    'BufferedOutput' => [new BufferedOutput(), false],
    'BufferedConsoleOutput' => [new BufferedConsoleOutput(), false],
    'PromptsBufferedConsoleOutput' => [new PromptsBufferedConsoleOutput(), false],
    'ConsoleOutput' => [new ConsoleOutput(), stream_isatty(STDIN)],
    'OutputStyle with ConsoleOutput' => [new OutputStyle(new ArrayInput([]), new ConsoleOutput()), stream_isatty(STDIN)],
    'OutputStyle with BufferedOutput' => [new OutputStyle(new ArrayInput([]), new BufferedOutput()), false]
]);
