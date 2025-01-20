<?php

namespace App\Factory;

/**
 * Factory class to instantiate model classes
 */

abstract class FModel extends FBase
{
    /**
     * @inheritdoc
     */
    public static function build(string $class = '', array $constructorParams = [])
    {
        $class = 'App\Models\\' . $class;
        return parent::build($class, $constructorParams);
    }
}