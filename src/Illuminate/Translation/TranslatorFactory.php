<?php

namespace Henzeb\Prompts\Illuminate\Translation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use RuntimeException;
use Throwable;

class TranslatorFactory
{
    public function make(string $locale, ?string $path = null, ?string $fallback = null): Translator
    {
        try {
            $filesystem = new Filesystem();

            $path ??= getcwd() . DIRECTORY_SEPARATOR . 'lang';

            $loader = new FileLoader(
                $filesystem,
                array_filter([
                    dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lang',
                    $path . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'prompts' . DIRECTORY_SEPARATOR . 'extras',
                    $path,
                ])
            );

            $loader->addNamespace(
                'prompts/extras',
                $path,
            );

            $translator = new Translator($loader, $locale);

            if ($fallback) {
                $translator->setFallback($fallback);
            }

            $translator->addLines([], $locale);

        } catch (Throwable $e) {
            if (preg_match('/Class \".*\" not found/i', $e->getMessage()) !== false) {
                throw new RuntimeException(
                    '"illuminate\validation" is not installed.'
                );
            }

            throw $e;
        }

        return $translator;
    }
}
