<?php

namespace App\Services\Traits;

/**
 * Update the profile columns if a new column is added or a old one is deleted
 */
use App\Factory\FModel;
use App\Models\BaseModel;
use App\Models\Matches;
use App\Models\MatchesForm;

trait TUpdateTableColumns
{
    /**
     * UpdateColumns
     *
     * @param BaseModel $model
     * @param string $env
     * @return void
     */
    public function updateColumns(BaseModel $model, string $env = ''): void
    {
        $class = get_class($model);
        $columns = array_column($model->getTableColumns(), 'Field');
        /** @var Matches $mMatches */
        $mMatches = FModel::build('Matches', [$env]);
        $matches = get_class($model) == 'App\Models\MatchesUsers'
            ? $mMatches->get(MatchesForm::$PERSONAL_DATA_FORM_ID, ['column' => 'matches_form_id'])
            : $mMatches->get(
                null,
                [
                    'filter' => [
                            'needle' => MatchesForm::$PERSONAL_DATA_FORM_ID,
                            'columns' => 'matches_form_id',
                            'operators' => ['matches_form_id' => '!=']]

                ]
            );
        $matches = array_column($matches, 'db_code');
        // Add new columns
        foreach ($matches as $match) {
            if (!in_array($match, $columns)
            ) {
                $model->addTableColumn($match, 'varchar(500)');
            }
        }
        // Delete non-existing columns
        foreach ($columns as $column) {
            if (!in_array($column, $matches) && !property_exists($model, $this->snakeToCamel($column))) {
                $model->dropTableColumn($column);
            }
        }
    }
}