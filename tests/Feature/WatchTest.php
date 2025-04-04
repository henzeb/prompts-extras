<?php

use Henzeb\Prompts\Prompt;
use Henzeb\Prompts\Watch;
use function Henzeb\Prompts\watch;
use function Laravel\Prompts\note;
use function Laravel\Prompts\text;

covers(Watch::class);

it('should render', function () {
    Prompt::fake();

    watch(function () {
        note('This should render');
    });

    Watch::assertSecondsSleptBetweenIntervals(0);

    Prompt::assertOutputContains('This should render');
});

it('should ignore interactive prompts', function () {
    Prompt::fake();

    watch(function () {
        text('A question.');
        note('This should render');
    });

    Prompt::assertOutputContains('This should render');
});

it('should render callable array', function () {
    Prompt::fake();

    $watch = new class() {
        public function watch(): void
        {
            note('This should render through array');
        }
    };

    watch([$watch, 'watch']);

    Watch::assertSecondsSleptBetweenIntervals(0);

    Prompt::assertOutputContains('This should render through array');
});

it('should render invokable', function () {
    Prompt::fake();

    $watch = new class() {
        public function __invoke(): void
        {
            note('This should render through invokable');
        }
    };

    watch($watch);

    Watch::assertSecondsSleptBetweenIntervals(0);

    Prompt::assertOutputContains('This should render through invokable');
});

it('should render buffered', function () {
    Prompt::fake();

    watch(function () {

        note('This should not render');

        (fn() => Watch::output())
            ->bindTo(null, Watch::class)()->fetch();
    });

    Prompt::assertOutputDoesntContain('This should not render');
});

it('should fake sleep when faking', function (
    int $expected,
    int $iteration,
    ?int $interval = null
) {
    Prompt::fake();

    Watch::fakeTimes($iteration);

    watch(function () {
    }, $interval);

    Watch::assertSecondsSleptBetweenIntervals($expected);
})->with(
    [
        ['expected' => 2, 'iteration' => 2, 'interval' => 2],
        ['expected' => 3, 'iteration' => 2, 'interval' => 3],
        ['expected' => 6, 'iteration' => 3, 'interval' => 3],
        ['expected' => 4, 'iteration' => 3, 'interval' => null],
    ]
);

it('should throw exception with a negative interval ', function () {
    Prompt::fake();

    watch(fn() => null, -1);

})->throws(ValueError::class);

it('should sleep 2 seconds by default', function () {
    Prompt::fake();

    Watch::fakeTimes(2);

    watch(function () {
    });

    Watch::assertSecondsSleptBetweenIntervals(2);
});

it('should actually sleep at intervals', function () {

    Prompt::fake();

    Watch::fakeTimes(2);

    Watch::shouldNotFakeIntervalSleep();

    $start = time();

    watch(function () {
        note('This should render');
    }, 1);

    $end = time();

    expect($end - $start)->toBe(1);

    Prompt::assertOutputContains('This should render');
});

it('should throw exception when invoking fakeTimes when not faked', function () {
    Watch::fakeTimes(2);
})->throws(RuntimeException::class);

it('should throw exception when invoking assertSecondsSleptBetweenIntervals when not faked', function () {
    Watch::assertSecondsSleptBetweenIntervals(2);
})->throws(RuntimeException::class);

it('should throw exception when invoking shouldNotFakeIntervalSleep when not faked', function () {
    Watch::shouldNotFakeIntervalSleep();
})->throws(RuntimeException::class);
