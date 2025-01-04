<?php

namespace Henzeb\Prompts\Illuminate\Validation;

use Henzeb\Prompts\Illuminate\Translation\TranslatorFactory;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

class ValidatorFactory extends Factory
{
    public function __construct(
        string  $locale,
        ?string $path,
        ?string $fallback
    )
    {
        parent::__construct(
            $this->loadTranslator($locale, $path, $fallback)
        );
    }

    protected function loadTranslator(string $locale, ?string $path, ?string $fallback): Translator
    {
        return (new TranslatorFactory())->make($locale, $path, $fallback);
    }
}
