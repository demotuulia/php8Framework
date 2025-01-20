<?php

namespace Commands;

/**
 * Base class for all commands
 * 
 *  Arguments
 *   env=test   : use this if you want to use test database, as default the dev is used
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../App/Lib/php8.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Plugins;
use App\Plugins\Db\Db;
use Commands\Traits\TDatabase;


abstract class BaseCommand
{
    use TDatabase;


    /**
     * @var Db
     */
    protected $db;

    /** @var array */
    protected $arg;

    abstract protected function run(): void;

    /**
     * construct
     * 
     * @param Db $db
     */
    public function __construct(array $argv)
    {
        $this->getArguments($argv);
        $env = isset($this->arg['env']) ?? '';
        $this->setDatabaseConnection($env);
        $this->run();
    }

    /**
     * get arguments frin command 
     *
     * @param array $arg
     * @return void
     */
    private function getArguments(array $arg): void
    {
        if (empty($arg)) {
            return;
        }
        foreach ($arg as $item) {
            $parts = explode('=', $item);
            if (count($parts) == 2) {
                $this->arg[current($parts)] = end($parts);
            }
        }
    }
}
