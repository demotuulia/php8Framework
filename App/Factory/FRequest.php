<?php

namespace App\Factory;

/**
 * Factory class to instantiate m different types of request type classes
 *
 * The supported types are:
 *
 * 'application/json'
 * 'application/x-www-form-urlencoded'
 * 'FormUrlencoded'
 *
 */

 use App\Enums\ERequestType;

abstract class FRequest extends FBase
{
    /**
     * @inheritdoc
     */
    public static function build(string $class = '', array $constructorParams = [])
    {
        $className = self::getClassName();
        $class = 'App\Services\Request\\' . $className;
        return parent::build($class);
    }

    /**
     * Get the class name, based on the format of the current request
     *
     * @throws \Exception
     */
    private static function getClassName(): string
    {
        if (str_starts_with($_SERVER['CONTENT_TYPE'], ERequestType::$JSON)) {
            return 'Json';
        }
        if (str_starts_with($_SERVER['CONTENT_TYPE'], ERequestType::$URL_ENCODED)) {
            return 'FormUrlencoded';
        }
        if (str_starts_with($_SERVER['CONTENT_TYPE'], ERequestType::$FORM)) {
            return 'Formdata';
        }
        
        throw new \Exception("Invalid type given.");
    }
}