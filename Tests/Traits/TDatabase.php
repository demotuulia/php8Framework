<?php

namespace Tests\Traits;

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
    private function setDatabaseConnection(): void
    {
        $this->db = Connection::getConnection('test');
    }

    /**
     * Initialize database
     *
     * Delete all data from the tables.
     *
     * @return void
     */
    private function initializeDatabase(): void
    {
        shell_exec(__DIR__ . '/../Scripts/initializeDatabase.sh');

        $this->db->executeQuery('SET foreign_key_checks = 0');
        $tables = $this->db->executeSelectQuery('show tables');

        foreach ($tables as $table) {
            $this->db->executeQuery('truncate ' . current($table));
        }
        $this->db->executeQuery('SET foreign_key_checks = 1');
    }

    /**
     * Set test data
     *
     * Initialize database with some test data.
     * See this data in
     * Tests/Scripts/sql/testContent.sql
     *
     * @return void
     */
    private function setTestData(): void
    {
        shell_exec(__DIR__ . '/../Scripts/insertTestContent.sh');
    }
}