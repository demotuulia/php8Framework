<?php

namespace App\Services;

use App\Enums\ELocales;

class TranslationsService extends BaseService
{

    private static ELocales $locale;

    public function __construct()
    {
    }

    public static function setLocale(ELocales $locale): void
    {
        self::$locale = $locale;
    }

    public static function getLocale(): ELocales
    {
        if (isset(self::$locale)) {
            return self::$locale;
        }   
        return ELocales::nl_NL;
    }

    public static function getLocaleName(): string
    {
        return self::getLocale()->name;
    }

    public static function _(string $key, array $replacements = [], string $category = 'default'): string
    {
        $path = __DIR__ . '/../../translations/' . self::getLocaleName() . '/' . $category . '.php';
        $translation = include($path);
        if (isset($translation[$key])) {
            return strtr($translation[$key], $replacements);
        }
        return $translation;
    }
}