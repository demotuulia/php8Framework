<?php

namespace Commands\Traits;

use App\Lib\DbConnection as Connection;
use App\Plugins;
use App\Plugins\Db\Db;

trait TDatabase
{
    /**
     * Set database connection
     *
     * @return void
     */
    private function setDatabaseConnection(string $env = ''): void
    {
        $this->db = Connection::getConnection($env);
    }


}