<?php

namespace Henzeb\Prompts\Illuminate\Providers;

use Closure;
use Henzeb\Prompts\Illuminate\Validation\Validator;
use Henzeb\Prompts\Inputs;
use Henzeb\Prompts\Prompt;
use Henzeb\Prompts\Support\Stacks\CommandStack;
use Henzeb\Prompts\Support\Stacks\OutputStack;
use Illuminate\Console\Command;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Prompts\Prompt as LaravelPrompt;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PromptsExtrasProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->setupTranslations();

        $this->setupValidator();

        $this->setupResolverHooks();

        $this->listenToCommandStart();

        $this->listenToCommandFinished();
    }

    /**
     * @return void
     */
    public function setupTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang/', 'prompts/extras');

        $this->publishes(
            [
                __DIR__ . '/../lang' => $this->app->langPath('vendor/prompts/extras'),
            ],
            'prompts-extras-translations'
        );
    }

    public function setupResolverHooks(): void
    {
        $this->app->beforeResolving(
            Command::class,
            function (string $command) {
                Inputs::setCommand($command);
                Inputs::setInput(new ArrayInput([]));
            }
        );

        $this->app->afterResolving(
            Command::class,
            function (Command $command) {
                Inputs::setCommand($command::class);

                Inputs::mergeDefinition($command->getDefinition());
            }
        );
    }

    public function listenToCommandStart(): void
    {
        Event::listen(
            CommandStarting::class,
            function (CommandStarting $event) {
                $command = Artisan::all()[$event->command] ?? null;

                if (!$command instanceof SymfonyCommand) {
                    return;
                }

                $command->setCode(
                    Closure::bind(function (InputInterface $input, OutputInterface $output): int {

                        Prompt::ValidateUsing(
                            (new Validator())(...)
                        );

                        CommandStack::add($this::class);

                        Inputs::setCommand(CommandStack::current());

                        OutputStack::add(Prompt::output());

                        Prompt::setOutput($output);

                        Prompt::updateInteractivity();

                        if ($input instanceof Input) {
                            Inputs::setInput($input);
                        }

                        Inputs::setDefinition($this->getDefinition());

                        Inputs::validateEarly();

                        return $this->execute($input, $output);

                    }, $command, Command::class)
                );
            }
        );
    }

    public function listenToCommandFinished(): void
    {
        Event::listen(
            CommandFinished::class,
            function () {
                if (CommandStack::hasCommands()) {
                    Inputs::setCommand(CommandStack::pop());
                }

                $output = OutputStack::hasOutputs() ? OutputStack::pop() : null;

                if ($output !== null) {
                    Prompt::setOutput($output);
                    Prompt::updateInteractivity();
                }
            }
        );
    }

    public function setupValidator(): void
    {
        LaravelPrompt::validateUsing(
            (new Validator())(...)
        );
    }
}
