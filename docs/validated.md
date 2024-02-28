# validated

Validated allows the user to give a value, validate it, and ask for the correct value
if no or incorrect value is given.

The code below would prompt for the correct value using the `text` prompt.

```php
use function Henzeb\Prompts\validated;

$value = validated(
    value: 'invalid',
    label: 'Tell me your name',
    validate: fn($value) => $value === 'invalid' 
);
```

Below example would prompt for input using `text` because it's value is required.

```php
use function Henzeb\Prompts\validated;

$value = validated(
    value: null,
    label: 'Tell me your name',
    required: true
);
```

## Specifying which prompt to use

Sometimes you need a little more customization of the prompt used.
You can specify the prompt using the `prompt` field. In most cases,
you can use the function name. If the prompt does not exist within
`Laravel Prompt` or `Prompts Extras`, you can specify a FQCN to the
prompt you require, or you can create a `callable` that calls your prompt.

Use the `options` field to set the options on the prompt used. See the
documentation of the prompt you want to use.

```php
use function Henzeb\Prompts\validated;

$value = validated(
    value: 'invalid',
    label: 'Give me your Password please.',
    required: 'Required for logging in.',
    validate: fn($value) => $value === 'invalid',
    prompt: 'password', 
    options: [
        'hint' => 'It is in your wallet...'
    ]   
);
```

Note: any options set in the `options` parameter overrides any of the
other parameters.

## Displaying validation errors

By default, `validated` will just prompt for input and show no errors when value is `null` when the value is
required. By setting the `submit` flag to true, the given prompt will show errors.

```php
use function Henzeb\Prompts\validated;

$value = validated(
    value: null,
    label: 'Your name?',
    required: 'Required.',
    submit: true
);
```

Note: `validated` will always display errors if the given value does not evaluate to null or false.
