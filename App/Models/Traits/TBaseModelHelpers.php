<?php

namespace App\Models\Traits;
/**
 * SQL statements for the models
 */
trait TBaseModelHelpers
{
    /**
     * Get table columns
     *
     * @return array
     */
    public function getTableColumns(): array
    {
        $query = 'SHOW COLUMNS FROM ' . $this->table;
        return $this->db->executeSelectQuery($query);
    }

    /**
     * Add colun to table
     *
     * @param string $name      name of the column
     * @param string $type      type
     * @return void
     */
    public function addTableColumn(string $name, string $type)
    {
        $query = 'ALTER TABLE ' . $this->table .
            ' ADD COLUMN  ' . $name .
            ' ' . $type;
        $this->db->executeQuery($query);
    }

    /**
     * Drop column
     *
     * @param string $name
     * @return void
     */
    public function dropTableColumn(string $name)
    {
        $query = 'ALTER TABLE ' . $this->table .
            ' DROP  ' . $name;
        $this->db->executeQuery($query);
    }

    /**
     * Get manx value of the table
     * 
     * @param string $column    column to select max value
     * @param string $filter    filter string as SQL
     * @return mixed
     */
    public function max(string $column, string $filter = '')
    {
        $query = 'SELECT max(`' . $column . '`) as max FROM ' . $this->table;
        if ($filter) {
            $query .= ' WHERE ' . $filter;
        }

        $result = $this->db->executeSelectQuery($query);
        return $result[0]['max'] != 0
            ? $result[0]['max'] + 1
            : 1;
    }

    
    /**
     * Truncate 
     *
     * @param string $table
     * @return void
     */
    public function truncate(string $table = ''): void
    {
        $query = 'SET foreign_key_checks = 0';
        $this->db->executeSelectQuery($query);

        if ($table == '') {
            $table = $this->table;
        }

        $query = 'TRUNCATE ' . ($table) ?? $this->table;
        $this->db->executeSelectQuery($query);
        $query = 'SET foreign_key_checks = 1';
        $this->db->executeSelectQuery($query);
    }
}
