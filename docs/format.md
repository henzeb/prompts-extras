# format

`format` allows to format text using the available colors, background-colors
and other styles in Prompts.

```php
use function Henzeb\Prompts\format;

format('my text', 'bold'); // creates a bold string

format('my text', 'bold', 'strikethrough'); // creates a bold string with strikethrough
```

you can add as many styles you wish. See for possible values here