<?php
/**
 * A class to implement php8 functions to older php versions
 */

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if (!$length) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }
}


if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return (bool)strpos($haystack, $needle);
    }
}

function error_log_array(array $arr)
{
    error_log(json_encode($arr));
}

function error_log_die($message)
{
    if (is_string($message)) {
        error_log($message);
    } else {
        error_log(json_encode($message));
    }
    die;

}

function dd($item = '')
{
    if (php_sapi_name() != 'cli') {
        echo '<pre style="font-size: 9px">';
    }
    $d = debug_backtrace();
    echo "\n" . $d[0]['file'] . ' line ' . $d[0]['line'] . "\n";
    var_dump($item);
    die;
}

function dump($item = '')
{
    if (php_sapi_name() != 'cli') {
        echo '<pre style="font-size: 9px">';
    }
    $d = debug_backtrace();
    echo "\n" . $d[0]['file'] . ' line ' . $d[0]['line'] . "\n";
    var_dump($item);
    if (php_sapi_name() != 'cli') {
        echo '</pre>';
    }
}