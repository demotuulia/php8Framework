<?php

namespace App\Services\Traits;

/**
 * This trait handles the matches db_codes
 */

 use App\Helpers\HConfig;
trait TEncrypt
{

    /**
     * Get the salt for the admin password encryption
     *
     * @return string
     */
    private static function adminSalt(): string
    {
        return  HConfig::getConfig('admin.passwordSalt'); require __DIR__ . '/../../../config/config.php';
    }

    /**
     * Encrypt the $item
     *
     * @param string $item
     * @param string $salt
     * @return string
     */
    private function crypt(string $item, string $salt): string
    {
        return crypt($item, $salt);
    }

    /**
     * Generate encrypt token
     *
     * @param integer $length
     * @return string
     */
    private static function generateToken(int $length = 90): string
    {
        return substr(
            str_shuffle(
                str_repeat(
                    $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length / strlen($x))
                )
            ),
            1,
            $length
        );
    }
}