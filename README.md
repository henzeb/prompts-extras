# Extras for Laravel Prompts

[![Latest Version on Packagist](https://img.shields.io/packagist/v/henzeb/prompts-extras.svg?style=flat-square)](https://packagist.org/packages/henzeb/prompts-extras)
[![Total Downloads](https://img.shields.io/packagist/dt/henzeb/prompts-extras.svg?style=flat-square)](https://packagist.org/packages/henzeb/prompts-extras)

This package offers additional functionalities that expand upon Laravel's 
Prompt. While these features may not be universally desired, they could 
prove valuable for specific use cases.

Just like Laravel Prompts, you don't need the Laravel framework to use this 
package.

The name is derived from `Busybox Extras` which has additional functionalities
for `BusyBox`, which is a single binary containing a lot of unix utilities for 
resource-constrained environments.

## Usage

### watch

The watch prompt lets you monitor any external process using tables and progress 
bars, just like the familiar watch command on Linux/Unix. This means you can 
track the progress of background tasks happening outside your current script.

Below code will repeatedly update a table with the current timestamp
```php
use function Henzeb\Prompts\watch;
use function Laravel\Prompts\table;

watch(
    render: function (){
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

### testing watch

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

Allows you to assert the total seconds slept between intervals. Allows users to test if they passed the correct value.

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

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email henzeberkheij@gmail.com instead of using the issue tracker.

## Credits

- [Henze Berkheij](https://github.com/henzeb)

## License

Prompts Extras is open-sourced software licensed under the [MIT license](LICENSE.md).
