# Input

While Laravel offers built-in features for handling (missing) command-line arguments,`Laravel Prompts` lacks dedicated
support for defining and validating them. This is where `Prompts Extras` comes in.

`Prompts Extras` simplifies the process of setting up command-line options and arguments. It empowers you to define
these elements, validate user input, and prompt for the correct value if necessary, enhancing the user experience and
reducing potential errors.

Note: You can use any of the methods below in Laravel and mix them with the regular features built into Laravel.

## Usage

### addArgument

Used to add arguments. Works exactly as in [Symfony](https://symfony.com/doc/current/console/input.html) as it is using
it under the hood, but has a few extra
parameters to specify how the field is going to be validated.

```php
use Symfony\Component\Console\Input\InputArgument;
use function Henzeb\Prompts\Input\addArgument;

addArgument(
    name:'name',
    mode: InputArgument::REQUIRED,
    description: 'Your name.',
    default: 'Taylor',
    suggestedValues: ['Taylor', 'Jess', 'Nuno'],
    validate: function () { 
        // ...
    },
    validateEarly: false, // this is the default,
    prompt: 'text', // this is the default,
    options: [
        'hint' => 'It does not have to be Laravel core team members.'
    ],
    submit: true // whether to show errors on null. False by default
);
```

### addOption

`addOption` works the same as `addArguments`

```php
use Symfony\Component\Console\Input\InputOption;
use function Henzeb\Prompts\Input\addOption;

addOption(
    name:'member',
    shortcut: ['m'], // can be string or array of strings
    mode: InputOption::VALUE_OPTIONAL,
    description: 'Select a member',
    default: 'Taylor',
    suggestedValues: ['Taylor', 'Jess', 'Nuno'],
    validate: function () { 
        // ...
    },
    validateEarly: false, // this is the default,
    prompt: 'select',
    options: [
        'options' => ['Taylor', 'Jess', 'Nuno']
        'hint' => 'Now it does have to be Laravel core team members.'
    ],
    submit: true // whether to show errors on null. False by default
);
```

### validateInputEarly

There are two ways to validate input. The first is on access using the `option` or`argument` functions
(see later in the documentation), or by calling `validateInputEarly`.

When this function is called, all options and arguments with the flag `validateEarly` set to true will be validated.

```php
use function Laravel\Prompts\info;
use function Henzeb\Prompts\Input\addOption;
use function Henzeb\Prompts\Input\validateInputEarly;

addOption(
    name: 'confirm',
    prompt: 'confirm',
    validateEarly: true,
    options: [
        'required' => true,
        'hint' => 'Select yes to continue.'
    ]
);

info('This goes first...');

validateInputEarly();
```

Note: by default for both the `addArgument` and `addOption` functions, `validate early` is set to false.

Note: In Laravel, you don't need to call this method, this is being done for
you.

### argument

The argument function returns the specified argument. If this was set using `Prompts Extras'` function `addArgument`
and validation is configured, it will validate the current value of the argument and prompt if needed, before
returning the value to the user.

```php
use function Henzeb\Prompts\Input\argument;

argument('name');

/** An optional parameter allows you to set a default */
argument('name', 'john'); 
```

### option

The option function returns the specified option. If this was set using
`Prompts Extras` function `addOption` and validation is configured,
it will validate the current value of the option and prompt if needed, before
returning the value to the user.

```php
use function Henzeb\Prompts\Input\option;

option('name');

/** An optional parameter allows you to set a default */
option('name', 'john'); 
```

### setArgument / setArguments

on runtime, you can set argument values. This method validates the given input and prompt when needed.

````php
use function Henzeb\Prompts\Input\setArgument;
use function Henzeb\Prompts\Input\setArguments;

setArgument('name', 'Taylor');

setArguments(['name' => 'Taylor', 'email'=>'taylor@laravel.com']);
````

### setOption / setOptions

on runtime, you can set option values. This method validates the given input and prompt when needed.

````php
use function Henzeb\Prompts\Input\setOption;
use function Henzeb\Prompts\Input\setOptions;

setOption('name', 'Taylor');

setOptions(['name' => 'Taylor', 'email'=>'taylor@laravel.com']);
````

### argumentSet

When an argument can be allowed `null` as value or has a default value,
it can be hard to tell if an argument was set. With this function you can
test if the user has actually passed the argument.

```php
use function Henzeb\Prompts\Input\argumentSet;

argumentSet('name'); // returns a boolean
```

### optionSet

When an option can be allowed `null` as value or has a default value,
it can be hard to tell if an option was set. With this function you can
test if the user has actually passed the option.

```php
use function Henzeb\Prompts\Input\InputoptionSet;

optionSet('name'); // returns a boolean
```

### validateArgumentUsing / validateOptionUsing

There are cases, like in Laravel, the options are already configured. Using
this method, you can still set up validation.

```php
use function Henzeb\Prompts\Input\validateArgumentUsing;
use function Henzeb\Prompts\Input\validateOptionUsing;

validateArgumentUsing(
    name: 'name',
    validate: function($value){
        // ... validation logic
    },
    validateEarly: true, // false by default
    prompt: 'select', // text by default,
    options: [] // options for the specified prompt
    submit: false // whether to show errors on prompt or not, false is default
);

validateOptionUsing(
    name: 'confirm',
    validate: function($value) {
        // ... validation logic
    },
    validateEarly: true, // false by default
    prompt: 'confirm', // text by default,
    options: [] // options for the specified prompt
    submit: false // whether to show errors on prompt or not, false is default
);

```

