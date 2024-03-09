<?php

namespace Henzeb\Prompts\Concerns;

use function function_exists;
use function is_int;
use function pcntl_async_signals;
use function pcntl_signal;

trait Pcntl
{
    protected function registerInterruptSignal(): void
    {
        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
        }

        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, function () {

                $this->moveCursor(-2);
                
                $result = isset(static::$cancelUsing) ? (static::$cancelUsing)() : null;
                $this->__destruct();

                if (is_int($result) && !static::isFaked()) {
                    exit($result);
                }

                static::terminal()->exit();
            });
        }
    }

    protected static function isPcntlSupported(): bool
    {
        return function_exists('pcntl_signal');
    }
}
