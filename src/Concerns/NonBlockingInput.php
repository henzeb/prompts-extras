<?php

namespace Henzeb\Prompts\Concerns;

use Mockery\MockInterface;

use function stream_set_blocking;

trait NonBlockingInput
{
    protected function disableBlockingInput(): void
    {
        stream_set_blocking(STDIN, false);
    }

    protected function handleKeyPresses(): void
    {
        $terminal = self::terminal();

        if ($terminal instanceof MockInterface && $terminal->mockery_getExpectationCount() === 0) {
            $terminal->expects('read')->atLeast()->once();
        }

        while (self::terminal()->read()) {
            $this->moveCursor(0, -1);
        }
    }
}
