# Laravel

There is a service provider which is auto-discovered. This allows`Laravel Prompts`
and `Prompts Extras` to be isolated within commands. This means that when you call `Artisan::call('YourCommand')`,
you don't have to worry about the correct output.

In addition, when buffered output is used, it will automatically set the interactive mode for Prompts to false.

Imagine the following command:

````php
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\InputOption;
use function Henzeb\Prompts\Input\addOption;
use function Laravel\Prompts\note;
use function Laravel\Prompts\text;

class YourCommand extends Command 
{
    public function configure(): void
    {
        $this->setName('your:command');
        
        addOption('print', mode: InputOption::VALUE_REQUIRED);
        addOption('call');
    }
    public function handle(): int {
        
        if(option('call')) {
            Artisan::call('YourCommand', ['--print'=> 'does not print']);
        }
        
        $text = text('a question'); // won't prompt when Artisan::call calls this method.
        
        note(option('print'));
        
        return 0;
    }
}
````

And then it can be called as such:

```bash
./artisan your:command --print 'Does print!'
```

## Validation

In Laravel, validation is done by Laravel's own validator. this means you can use Laravel's validation
rules, just like you are used to.

```php
use Illuminate\Validation\Rule;
use function Laravel\Prompts\text;

text('your email?', validate: 'required|email');

text('your email?', validate: ['required', 'email']);

text('your email?', validate: Rule::when(true, 'nullable|email'));
```

Note: This is configured using Prompts `validateUsing` method. You can override this at any time.

Note: When using the Validator, the options validate regardless of being required or not.
Configure our validation rules accordingly.

## Translations

Laravel's validation rules have messages optimized for web. `Prompts Extras` has validation
rule messages optimized for console. you can find them in the namespace
`prompts\extras::validation`.

You can also publish the translation files:

```bash
php artisan vendor:publish --tag=prompts-extras-translations
```

Note: the `:attribute` is removed. If you add it again, it will return `input` by default.
You can change that by overriding the `prompts\extras::prompts.attribute` translation.

## Validate With

The default messages are great, but if you need specific messages, you can use `Validate::with`

For example, below would print `please enter something in this name field...`.

```php
use Henzeb\Prompts\Illuminate\Validation\Validate;
use function Laravel\Prompts\text;

text(
    label: 'name',
    validate: Validate::with(
        rules: 'required|min:3',
        messages: [
            'required' => 'please enter something in :attribute...'
        ],
        attribute: 'this name field'   
    )
);

```

Note: `messages` and `attributes` are optional.
