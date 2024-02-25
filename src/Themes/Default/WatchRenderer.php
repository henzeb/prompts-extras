<?php

namespace Henzeb\Prompts\Themes\Default;

use Closure;
use Laravel\Prompts\Output\BufferedConsoleOutput;
use Laravel\Prompts\Prompt;
use Laravel\Prompts\Themes\Default\Renderer;
use Symfony\Component\Console\Output\OutputInterface;

class WatchRenderer extends Renderer
{
    /**
     * buffers the output generated inside the Closure and flushes the output to Prompt
     * in order to utilize Prompts neat way of updating lines
     */
    public function __invoke(Closure $render, OutputInterface $originalOutput): string
    {
        $bufferedOutput = new BufferedConsoleOutput();

        Prompt::setOutput($bufferedOutput);

        $render();

        Prompt::setOutput($originalOutput);

        return $bufferedOutput->fetch();
    }
}
