<?php

namespace App\Helpers;

/**
 * A helper to write log statements to database
 */

use App\Factory\FModel;
use App\Models\LogStatements;

class HLogStatement
{
    public static function set(string $message, string $type = null): void
    {
        if (!HConfig::getConfig('setLogStatemnts')) {
            return;
        }
        if (!$type) {
            $type = LogStatements::$INFO;
        }

        $trace = debug_backtrace();

        /** @var LogStatements $mLogStatements */
        $mLogStatements = FModel::build('LogStatements');
        $mLogStatements->insert([
            'message' => substr($message, 0, 1000),
            'type' => $type,
            'file' => $trace[0]['file'] . ', line : ' . $trace[0]['line']
        ]);
    }
}
