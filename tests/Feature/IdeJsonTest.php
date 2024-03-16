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
    $php = (new PhpExecutableFinder)->find();
    $process = new Process([$php, __DIR__ . '/../../generate-ide.php', tempIdeJsonFile()]);
    $process->run();
}

it('should generate current ide.json', function () {
    generateTemporaryIdeJsonFile();
    
    expect(
        file_get_contents(IdeJsonFile())
    )->toBe(
        file_get_contents(tempIdeJsonFile())
    );
});
