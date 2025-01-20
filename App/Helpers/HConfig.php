<?php

namespace App\Helpers;

/**
 * A helper to read config variables 
 */

class HConfig
{
    /**
     * @return mixed
     */
    public static function getConfig(string $param)
    {
        $path = explode('.', $param);
        $config = require __DIR__ . '/../../config/config.php';
        $first = current($path);
        if (!isset($config[$first])) {
            return null;
        }

        foreach ($path as $item) {
            if (isset($config[$item])) {
                $config = $config[$item];
            } else {
                return null;
            }
        }
        return $config;
    }
}
