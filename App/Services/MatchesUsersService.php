<?php

namespace App\Services;

Use App\Services\Traits\TUpdateTableColumns;
use App\Traits\TString;

class MatchesUsersService extends BaseService
{
    use TUpdateTableColumns;
    use TString;

    protected $modelName = 'MatchesUsers';

    /**
     * Update the table matches_users with the columns of matches.db_code for the form id = 0
     *
     * @param string $env use 'test' if you create a model in test environment
     * @return void
     * @throws \Exception
     */
    public function updateUserColumns(string $env = ''): void
    {
        $this->updateColumns($this->model, $env);
    }

}