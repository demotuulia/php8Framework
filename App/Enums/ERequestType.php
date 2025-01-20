<?php

namespace App\Enums;
/**
 * Supportef API reuqest conten formats
 */
class ERequestType extends BaseEnum
{

    public static $JSON = 'application/json';
    public static $URL_ENCODED = 'application/x-www-form-urlencoded';
    static $FORM = 'multipart/form-data';
}
