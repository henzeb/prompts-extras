<?php

namespace Tests\Unit\Illuminate\Providers;

use Henzeb\Prompts\Illuminate\Providers\PromptsExtrasProvider;
use Henzeb\Prompts\Illuminate\Validation\Validate;
use Henzeb\Prompts\Inputs;
use Henzeb\Prompts\Prompt;
use Illuminate\Console\BufferedConsoleOutput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Lang;
use Laravel\Prompts\Key;
use Tests\Unit\Illuminate\Stubs\FirstTestCommand;
use Tests\Unit\Illuminate\Stubs\SecondTestCommand;
use function base_path;
use function expect;
use function Henzeb\Prompts\Input\addArgument;
use function Henzeb\Prompts\Input\addOption;
use function Henzeb\Prompts\Input\option;
use function it;
use function resolve;
use function trans;


uses(ProviderTestCase::class);

it('should set command before resolve', function () {

    resolve(FirstTestCommand::class);

    $command = Inputs::currentCommand();

    expect($command->position)->toBe(0)
        ->and($command->name)->toBe(FirstTestCommand::class);
});

it('should configure definitions', function () {

    $command = resolve(FirstTestCommand::class);

    $command->getDefinition()->getArgument('name');

    $command->getDefinition()->getOption('confirm');

})->doesNotPerformAssertions();

it('should isolate command definitions', function () {

    $firstCommand = resolve(FirstTestCommand::class);

    $secondCommand = resolve(SecondTestCommand::class);

    expect($firstCommand->getDefinition()->hasArgument('name'))->toBeTrue('first command does not have name')
        ->and($firstCommand->getDefinition()->hasOption('confirm'))->toBeTrue('first command does not have confirm')
        ->and($secondCommand->getDefinition()->hasArgument('name'))->toBeFalse('second command has name')
        ->and($secondCommand->getDefinition()->hasOption('confirm'))->toBeFalse('second command has confirm')
        ->and($firstCommand->getDefinition()->hasArgument('engine'))->toBeFalse('first command has engine')
        ->and($firstCommand->getDefinition()->hasOption('enable'))->toBeFalse('first command has enable')
        ->and($secondCommand->getDefinition()->hasArgument('engine'))->toBeTrue('second command does not have engine')
        ->and($secondCommand->getDefinition()->hasOption('enable'))->toBeTrue('second command does not have enable');
});

it('´should validate early', function () {
    Inputs::fake();
    $this->registerCommands();

    $this->artisan('first')
        ->expectsQuestion('confirm', 'yes');
});

it('´should isolate input and output between commands', function () {

    $this->registerCommands();

    Artisan::call('second', ['engine' => 'electric', '--enable' => true], $buffer = new BufferedConsoleOutput());

    $content = $buffer->fetch();

    expect($content)->toBe("\n engine electric\n\n");
});

it('should configure validator', function () {
    Prompt::fake([Key::CTRL_C]);
    Inputs::fake(['--email' => 'not an email']);

    addOption('email', validate: 'email', submit: true);

    option('email');

    Prompt::assertOutputContains(trans('prompts/extras::validation.email'));
});

it('should use validator and succeed', function () {
    Prompt::fake();
    Inputs::fake(['--email' => 'email@gmail.com']);

    addOption('email', validate: 'email', submit: true);

    option('email');

    Prompt::assertOutputDoesntContain(trans('prompts/extras::validation.email'));
});

it('should use validator and fail with required_argument', function () {
    Prompt::fake(['email@gmail.com', Key::ENTER]);
    Inputs::fake(['name' => 'Taylor']);

    addArgument('name');
    addOption('email', validate: 'required_if:name,Taylor|email', submit: true);

    option('email');

    Prompt::assertOutputDoesntContain(trans('prompts/extras::validation.email'));
});

it('should use validator and succeed with required_argument', function () {
    Prompt::fake();
    Inputs::fake(['--email' => 'email@gmail.com', 'name' => 'john']);

    addArgument('name');
    addOption('email', validate: 'required_if:name,john|email', submit: true);

    option('email');

    Prompt::assertOutputDoesntContain(trans('prompts/extras::validation.email'));
});

it('should configure validator with custom error message', function () {
    Prompt::fake([Key::CTRL_C]);
    Inputs::fake(['--email' => 'not an email']);

    Lang::addLines(
        [
            'validation.email' => 'bummer, this is not a valid email'
        ],
        'en',
        'prompts/extras'
    );

    addOption('email', validate: 'email', submit: true);

    option('email');

    Prompt::assertOutputContains('bummer, this is not a valid email');

});

it('should validate with custom messages and attribute', function () {
    Prompt::fake([Key::CTRL_C]);
    Inputs::fake(['--email' => 'not an email']);


    addOption(
        'email',
        validate: Validate::with(
            rules: 'email',
            messages: ['email' => 'this is an invalid :attribute'],
            attribute: 'Email Address'
        ),
        submit: true
    );

    option('email');

    Prompt::assertOutputContains('this is an invalid Email Address');

});

it('should publish the language files', function () {

    Artisan::call('vendor:publish', ['--provider' => PromptsExtrasProvider::class]);

    $prompts = resolve(Filesystem::class)->isFile(base_path('lang/vendor/prompts/extras/en/prompts.php'));
    $validation = resolve(Filesystem::class)->isFile(base_path('lang/vendor/prompts/extras/en/validation.php'));

    expect($prompts)->toBeTrue('prompts is not published')
        ->and($validation)->toBeTrue('validation is not published');

})->tearDownTheEnvironmentUsing(function () {
    resolve(Filesystem::class)->deleteDirectory(base_path('lang/vendor'));
});

it('should publish the language files using tag', function () {

    Artisan::call('vendor:publish', ['--tag' => 'prompts-extras-translations']);

    $prompts = resolve(Filesystem::class)->isFile(base_path('lang/vendor/prompts/extras/en/prompts.php'));
    $validation = resolve(Filesystem::class)->isFile(base_path('lang/vendor/prompts/extras/en/validation.php'));

    expect($prompts)->toBeTrue('prompts is not published')
        ->and($validation)->toBeTrue('validation is not published');

})->tearDownTheEnvironmentUsing(function () {
    resolve(Filesystem::class)->deleteDirectory(base_path('lang/vendor'));
});


