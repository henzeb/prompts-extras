<?php

use Henzeb\Prompts\Prompt;
use Illuminate\Console\BufferedConsoleOutput;
use Illuminate\Console\OutputStyle;
use Laravel\Prompts\Key;
use Laravel\Prompts\Output\BufferedConsoleOutput as PromptsBufferedConsoleOutput;
use Laravel\Prompts\Output\ConsoleOutput;
use Laravel\Prompts\Prompt as LaravelPrompt;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use function Henzeb\Prompts\format;
use function Laravel\Prompts\text;


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

it('should not fail when setting validator outside of laravel context', function () {
    Prompt::validateUsing(function () {

    });
})->doesNotPerformAssertions();

it('should setup illuminate validator', function (): void {
    Prompt::useLaravelValidator();

    Prompt::fake(['a', Key::ENTER, Key::BACKSPACE, 0, Key::ENTER]);

    text('test', validate: 'integer');

    Prompt::assertOutputContains('⚠ It must be an integer.');
});

it('should render format with bold text', function () {
    $formatted = format('bold text', 'bold');
    expect($formatted)->toBe('[1mbold text[22m');
});

it('should render format with green strikethrough text', function () {
    $formatted = format('bold text', 'green', 'strikethrough');
    
    expect($formatted)->toBe('[9m[32mbold text[39m[29m');
});