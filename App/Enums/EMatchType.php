<?php

namespace App\Enums;

/**
 * Matche types
 */

class EMatchType extends BaseEnum
{
    // All matches
    public static $CHECK_BOX = 'CHECK_BOX';
    public static $MULTIPLE_CHOOSE = 'MULTIPLE_CHOOSE';
    public static $MULTIPLE_CHOOSE_OR = 'MULTIPLE_CHOOSE_OR';
    public static $RADIO_BUTTON = 'RADIO_BUTTON';
    public static $EQUAL = 'EQUAL';

    public static $RANGE = 'RANGE';
    public static $BIGGER_THAN = 'BIGGER_THAN';
    public static $SMALLER_THAN = 'SMALLER_THAN';
    public static $DATE_FROM = 'DATE_FROM';
    public static $DATE_TO = 'DATE_TO';
    public static $MENU = 'MENU';
    public static $DESCRIPTION = 'DESCRIPTION';
    public static $SHORT_TEXT = 'SHORT_TEXT';

    // Tested and implemented
    static function frontEndSupported(): array
    {
        return [
            self::$MULTIPLE_CHOOSE_OR,
            self::$MENU,
            self::$DESCRIPTION,
            self::$SHORT_TEXT,
            self::$RADIO_BUTTON,
            self::$DATE_FROM,
        ];
    }

    /**
     * Matches which are not used as mathes, but as tect fields
     *
     * @return array
     */
    public static function noMatches(): array
    {
        return [
            self::$DESCRIPTION,
            self::$SHORT_TEXT,
        ];
    }

    /**
     * Is the current type matchable (can be searches as a match)
     *
     * @param string $type
     * @return boolean
     */
    public static function isMatchable(string $type): bool
    {
        if (!in_array($type, self::values())) {
            return false;
        }
        return !in_array($type, self::noMatches());
    }
}
