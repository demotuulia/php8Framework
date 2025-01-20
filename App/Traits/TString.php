<?php

namespace App\Traits;

/**
 * This trait has some string conversion functions
 */
trait TString
{
    /**
     * snake_to_camel => snakeToCamel
     *
     * @param [type] $input
     * @param boolean $startWithCapital
     * @return void
     */
    function snakeToCamel($input, bool $startWithCapital = false)
    {
        $input = strtolower($input);
        $input = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
        if ($startWithCapital) {
            $input = ucfirst($input);
        }
        return $input;
    }

    /**
     * Fulle name as string, includinf the dutch family name prefix
     *
     * @param array $params
     * @return string
     */
    private function fullName(array $params): string
    {
        $fullName = $params['name'];
        if (isset($params['prefix'])) {
            $fullName .= ' ' . $params['prefix'];
        }
        if (isset($params['family_name'])) {
            $fullName .= ' ' . $params['family_name'];
        }
        return $fullName;
    }

    /**
     * Explode string items to array by ' ' , ','  and '.'
     *
     * @param string $string
     * @return array
     */
    public function stringToArray(string $string): array
    {
        return  explode(
            ' ',
            strtolower(
                str_replace([',', '.'], '', $string)
            )
        );
    }


    /**
     * Genrate a html link
     *
     * @param string $href
     * @param string|null $name
     * @return string
     */
    public function htmlLink(string $href, string $name = null): string
    {
        $name = ($name != null) ? $name : $href;
        return '<a href="' . $href . '">' . $name . '</a>';
    }
}
