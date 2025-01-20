<?php

namespace App\Lib;
/**
 * Get database connection
 */
use App\Plugins;
use App\Plugins\Db\Db;

class DbConnection
{
    /**
     * @var null|Db
     *
     */
    static private $connection = null;

    /**
     * return App\Plugins\Db\Db|Db
     */
    public static function getConnection(string $env = '')
    {
        if ($env == '') {
            if (isset($_GET['env'])) {
                $env = ($_GET['env']);
            }
        }

        if (self::$connection === null) {
            $configFile = ($env != '')
                ? 'config' . ucfirst($env) . '.php'
                : 'config.php';
            $config = require __DIR__ . '/../../config/' . $configFile;
            $dbConfig = $config['db'];
            $connectionInterface = new Plugins\Db\Connection\Mysql(
                $dbConfig['host'],
                $dbConfig['database'],
                $dbConfig['username'],
                $dbConfig['password']
            );

            self::$connection = new Db($connectionInterface);
        }
        return self::$connection;

    }
}