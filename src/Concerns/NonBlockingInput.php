<?php

namespace Henzeb\Prompts\Concerns;

use Mockery\MockInterface;
use function stream_set_blocking;

trait NonBlockingInput
{
    protected function disableBlockingInput(): void
    {
        stream_set_blocking(STDIN, 0);
    }

    protected function handleKeyPresses(): void
    {
        if (static::isFaked()) {
            /**
             * @var $terminal MockInterface
             */
            $terminal = self::terminal();
            if ($terminal->mockery_getExpectationCount() === 0) {
                $terminal->expects('read')->atLeast()->once();
            }
        }

        while ($data = self::terminal()->read()) {
            $this->moveCursor(0, -1);
        }
    }
}
