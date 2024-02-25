<?php

namespace Henzeb\Prompts;

use Closure;
use Henzeb\Prompts\Themes\Default\WatchRenderer;
use Mockery\MockInterface;
use PHPUnit\Framework\Assert;
use RuntimeException;
use ValueError;

class Watch extends Prompt
{
    /**
     * How many times to fake an iteration.
     */
    protected static int $fakeTimes = 1;

    /**
     * count of faked iterations.
     */
    protected int $fakedTimes = 0;

    /**
     * Faking sleep or not.
     */
    protected static bool $fakeSleep = true;

    /**
     * The amount of seconds slept during intervals in total.
     */
    protected static int $sleptSeconds = 0;

    /**
     * The closure to execute on interval.
     */
    protected Closure $render;

    /**
     * The interval between updates.
     */
    protected int $interval;

    /**
     * Create a new Watch instance.
     */
    public function __construct(callable $render, ?int $interval = 2)
    {
        static::$sleptSeconds = 0;
        $this->render = $render(...);
        $this->interval = $interval ?? 2;

        if ($this->interval < 0) {
            throw new ValueError('watch interval must be greater than or equal to 0');
        }

        static::setRenderer(WatchRenderer::class);
    }
    /**
     * displays the watched output and updates after the specified interval.
     */
    public function prompt(): bool
    {
        $faked = static::isFaked();

        static::interactive(false);

        while (!$faked || $this->fakedTimes < static::$fakeTimes) {

            parent::render();

            if ($faked) {
                $this->fakedTimes++;

                if ($this->fakedTimes >= static::$fakeTimes) {

                    static::$fakeSleep = true;
                    break;
                }

                if (static::$fakeSleep) {
                    static::$sleptSeconds += $this->interval;
                    continue;
                }
            }

            sleep($this->interval);
        }

        return true;
    }

    /**
     * Render the prompt using the active theme.
     * Overrides default behaviour to pass along the current output.
     */
    protected function renderTheme(): string
    {
        $renderer = static::getRenderer();

        return $renderer($this->render, static::output());
    }

    /**
     * Get the value of the prompt.
     */
    public function value(): bool
    {
        return true;
    }

    /**
     * Tell Prompt how many iterations to fake.
     */
    public static function fakeTimes(int $times): void
    {
        if (!static::isFaked()) {
            throw new RuntimeException('Prompt must be faked before faking iterations.');
        }

        static::$fakeTimes = $times;
    }

    /**
     * Asserts the amount of seconds slept during intervals in total.
     */
    public static function assertSecondsSleptBetweenIntervals(int $seconds): void
    {
        if (!static::isFaked()) {
            throw new RuntimeException('Prompt must be faked before asserting.');
        }

        Assert::assertEquals($seconds, static::$sleptSeconds);
    }

    /**
     * By default, when Prompt is faked, the intervals are faked.
     * Use this to actually sleep between updates.
     */
    public static function shouldNotFakeIntervalSleep(): void
    {
        if (!self::isFaked()) {
            throw new RuntimeException('Not faking sleep makes no sense when not faking Prompt.');
        }

        static::$fakeSleep = false;
    }
}
