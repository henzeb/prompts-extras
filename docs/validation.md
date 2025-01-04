# Validation

Laravel Prompts supports validation out of the box, and with Laravel, Prompts Extras
configures it's improved validator. But outside Laravel, you can benefit from Laravel's
validator as well.

## installation

The following dependencies are required:

```bash
composer require illuminate/filesystem
composer require illuminate/translation
composer require illuminate/validaton
```

## usage

When the required packages are installed, simply use the following in your project:

```php
use Henzeb\Prompts\Prompt;

Prompt::useLaravelValidator();
```

### custom validation messages

The English language is used for translation by default. All translations should be available.
You can specify a locale and a path to your own translations directory if needed.

```php
use Henzeb\Prompts\Prompt;

Prompt::useLaravelValidator('nl', 'path/to/translations/');
```

### fallback locale

By default, the english language is specified as fallback. But you can configure any
language you have available by setting the third parameter.

```php
use Henzeb\Prompts\Prompt;

Prompt::useLaravelValidator('nl', 'path/to/translations/', 'fr');
```