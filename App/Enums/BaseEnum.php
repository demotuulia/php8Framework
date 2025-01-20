<?php

namespace App\Enums;
/**
 * Base of all enums
 * 
 * Made for php 7
 */
abstract class BaseEnum
{

    public static function value(string $name)
    {
        return constant("self::$name");
    }

    public static function values(): array
    {
        return array_keys(get_class_vars(get_called_class()));
    }
}
