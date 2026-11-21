<?php

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

uses()->afterEach(
    function () {
        if (file_exists(tempIdeJsonFile())) {
            unlink(tempIdeJsonFile());
        }
    }
);
function tempIdeJsonFile(): string
{
    return sys_get_temp_dir() . '/ide.json';
}

function ideJsonFile(): string
{
    return __DIR__ . '/../../ide.json';
}

function generateTemporaryIdeJsonFile(): void
{
    $php = (new PhpExecutableFinder())->find();
    $process = new Process([$php, __DIR__ . '/../../generate-ide.php', tempIdeJsonFile()]);
    $process->run();
}

function expectIdeJsonContains(mixed $expected, mixed $actual, string $path = '$'): void
{
    if (!is_array($expected)) {
        expect($actual)->toBe($expected, $path);

        return;
    }

    expect($actual)->toBeArray($path);

    if (array_is_list($expected)) {
        foreach ($expected as $index => $value) {
            if (is_array($value)) {
                expectIdeJsonContains($value, $actual[$index] ?? null, $path . '[' . $index . ']');
                continue;
            }

            expect($actual)->toContain($value);
        }

        return;
    }

    foreach ($expected as $key => $value) {
        expect($actual)->toHaveKey($key);
        expectIdeJsonContains($value, $actual[$key], $path . '.' . $key);
    }
}

it('should contain everything generated for the installed prompts version in ide.json', function () {
    generateTemporaryIdeJsonFile();

    expectIdeJsonContains(
        json_decode(file_get_contents(tempIdeJsonFile()), true),
        json_decode(file_get_contents(ideJsonFile()), true),
    );
});
