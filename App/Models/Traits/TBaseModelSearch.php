<?php
namespace App\Models\Traits;

/**
 * Sub functions for the function  BaseModel::get by adding SQL to it
 */

trait TBaseModelSearch
{
    /**
     * Filter by the operator equals
     *
     * @param string $query     Current SQL
     * @param string $column    Column to filter
     * @param [type] $value     Value to filter
     * @return string
     */ 
    protected function equals(string $query, string $column, $value = null): string
    {
        $query .= ' WHERE ' . $column;
        $query .= is_array($value)
            ? ' IN ("' . implode('","', $value) . '")'
            : ' ="' . $value . '"';
        return $query;
    }

    /**
     * Wildcard search in string
     * 
     * @param string $query                Current SQL
     * @param array $wildCardOptions       See options in function BaseModel::get
     * @param [type] $value
     * @return string
     */
    protected function wildcard(string $query, $wildCardOptions, $value = null): string
    {
        $query .= (!is_null($value)) ? ' AND' : ' WHERE';

        $wildcards = [];
        foreach (explode(',', $wildCardOptions['columns']) as $column) {
            foreach (explode(' ', $wildCardOptions['needle']) as $needle) {
                $wildcards[] = ' `' . $column . '` LIKE "%' . $needle . '%"';
            }
        }
        return $query . '( ' . implode(' OR ', $wildcards) . ')';
    }

    /**
     * Filter by adding an 'AND' condition
     *
     * @param string $query
     * @param array $options
     * @param string $value
     * @return string
     */
    protected function filter(string $query, $options, $value = null): string
    {
        $query .= (!is_null($value)) ? ' AND' : ' WHERE';

        $filters = [];
        foreach (explode(',', $options['columns']) as $column) {
            foreach (explode(' ', $options['needle']) as $needle) {
                $operator = $options['operators'][$column] ?? '=';
                $filters[] = $needle != 'null'
                    ? ' `' . $column . '` ' . $operator . ' "' . $needle . '" '
                    : ' `' . $column . '` ' . $this->filterByNull($operator);
            }
        }
        return $query . '( ' . implode(' OR ', $filters) . ')';
    }

    /**
     * filterBy 'null'
     *
     * @param string $operator
     * @return string
     */
    private function filterByNull(string $operator): string
    {
        return $operator == '=' ? 'is null' : ' in not null';
    }

    protected function orderBy(string $query, array $orderBy): string
    {
        $orderBys = [];
        foreach ($orderBy as $orderColumn => $direction) {
            $orderBys[] = $orderColumn . ' ' . $direction;
        }
        $query .= ' ORDER BY ' . implode(',', $orderBys);
        return $query;
    }

    /**
     * Add join relations
     *
     * @param array $with
     * @param array $items
     * @return array
     */
    protected function with(array $with, array $items): array
    {
        foreach ($items as &$item) {
            if ($with) {
                foreach ($with as $option) {
                    $itemOptions = $this->$option($item['id']);
                    if (!empty($itemOptions)) {
                        $item[$option] = $itemOptions;
                    }
                }
            }
        }
        return $items;
    }

    /**
     * Paginate (slice) query results for the current page
     *
     * @param array $items          items
     * @param array $pagination     array including optional params:
     *                                  [
     *                                      'current_page' => x,
     *                                      'page_size' => y,
     *                                  ]
     * @return array
     */
    protected function paginate(array $items, array $pagination): array
    {
        $currenPage = $pagination['current_page'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 15;
        return [
            'currenPage' => $currenPage,
            'pageSize' => $pageSize,
            'items' => array_slice($items, $pageSize * $currenPage, $pageSize),
        ];
    }

    /**
     * Standard array to return the paginataion result in the APU response
     *
     * @param array $items         
     * @param integer $totalCount   
     * @param integer $pageSize
     * @param integer $currenPage
     * @return array
     */
    protected function paginationResult(
        array $items,
        int   $totalCount,
        int   $pageSize,
        int   $currenPage
    ): array
    {
        return [
            'total' => $totalCount,
            'page' => $currenPage,
            'page_size' => $pageSize,
            'number_of_pages' => ceil($totalCount / $pageSize),
            'items_count' => count($items),
            'items' => $items,
        ];
    }
}
