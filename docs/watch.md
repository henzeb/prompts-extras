# watch

The watch prompt lets you monitor any external process using tables and progress
bars, just like the familiar watch command on Linux/Unix. This means you can
track the progress of background tasks happening outside your current script.

## Usage

Below code will repeatedly update a table with the current timestamp. You can
end it by pressing CTRL+C.

```php
use function Henzeb\Prompts\watch;
use function Laravel\Prompts\table;

watch(
    render: function () {
        table(
            ['current date'],
            [
                [(new Datetime())->format(DateTime::RFC850)]
            ]
        );
    },
    interval: 1 // default interval is 2
);
```

Note: `watch` ignores all interactive prompts that require user input.

### cancelUsing

Under the hood, to allow `cancelUsing` to function, it uses PCNTL. If you haven't
installed the extension or are using it on Windows, you cannot use `cancelUsing`.

### Testing watch

### fakeTimes

When Prompts is faked, `watch` will iterate only once. You can control this
by using `fakeTimes`.

```php
use Laravel\Prompts\Prompts;
use Henzeb\Prompts\Watch;
use function Henzeb\Prompts\watch;
use function Laravel\Prompts\info;

Prompts::fake();
Watch::fakeTimes(2);

watch(
    function(){
       info('hello world');
    }
);

Prompts::content(); // would contain hello world twice
```

### assertSecondsSleptBetweenIntervals

Allows you to assert the total seconds slept between intervals. Allows users to
test if they passed the correct value.

````php
use Laravel\Prompts\Prompts;
use Henzeb\Prompts\Watch;
use function Henzeb\Prompts\watch;
use function Laravel\Prompts\info;

Prompts::fake();
Watch::fakeTimes(2);

watch(
    function(){
       info('hello world');
    }
);

Watch::assertSecondsSleptBetweenIntervals(4); // would succeed because default interval is 2 and it iterated twice.
````
