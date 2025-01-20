<?php

namespace App\Services\Traits;


/**
 * This trait handles the matches db_codes
 */
trait TMatchesDbCode
{
    /**
     * Create unqiue column name by this code for matches profile rows
     *
     * @param integer $matchesFromId
     * @param string $label
     * @return string
     */
    public static function createMatchesDbCode(int $matchesFromId, string $label): string
    {
        return 'f' . $matchesFromId . '_' .
            preg_replace(
                "/[^a-zA-Z]/",
                "",
                substr($label, 0, 20)
            ) .
            substr(uniqid(), 0, 8);
    }

    public static function getFormIdFromMatchesDbCode(string $dbCode): int
    {
        return 1;
    }
}