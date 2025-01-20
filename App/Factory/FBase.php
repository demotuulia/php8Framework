<?php
namespace App\Factory;

/**
 * A generic factory class,
 */

abstract class FBase
{

    /**
     * @param string $class                 full name class (including namespace)
     * @param array $constructorParams      of the class
     * @return mixed
     * @throws \Exception
     */
    public static function build(string $class = '', array $constructorParams = [])
    {
        if (class_exists($class) || true) {
            if (!empty($constructorParams)) {
                $newClass = new \ReflectionClass($class);
                return $newClass->newInstanceArgs($constructorParams);;
            }
            return new $class();;
        } else {
            throw new \Exception("Invalid type given: " . $class);
        }
    }
}
