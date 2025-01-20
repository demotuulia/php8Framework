<?php

namespace App\Factory;

/**
 * Factory class to  instantiate service classes
 *
 */

abstract class FService extends FBase
{
    /**
     * @inheritdoc
     */
    public static function build(string $class = '', array $constructorParams = [])
    {
        $class = 'App\Services\\' . $class;
        return parent::build($class, $constructorParams);
    }
}
