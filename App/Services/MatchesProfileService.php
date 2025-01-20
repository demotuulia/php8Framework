<?php

namespace App\Services;

use App\Factory\FModel;
use App\Factory\FService;
use App\Models\Matches;
use App\Models\MatchesComments;
use App\Models\MatchesForm;
use App\Models\MatchesOptions;
use App\Models\MatchesOptionValues;
use App\Models\MatchesUsers;
use App\Services\Traits\TMatchesProfileEmails;
use App\Services\Traits\TMatchesProfileTags;
use App\Services\Traits\TUpdateTableColumns;
use App\Traits\TString;

class MatchesProfileService extends BaseService
{
    use TMatchesProfileEmails;
    use TMatchesProfileTags;
    use TString;
    use TUpdateTableColumns;

    protected $modelName = 'MatchesProfile';

    public static $matchesCommentsExtension = 'matches_comment_';
    public static $matchesUsersExtension = 'f0_';

    /**
     * Search profiles by the given $value and options
     *
     * See more explanation baseModel::get
     * 
     * @param mixed $value
     * @param array $options
     * @return array
     */
    public function search($value = null, array $options = []): array
    {
        $profiles = $this->model->get($value, $options);

        /** @var MatchesProfileStatusService $matchesProfileStatusService */
        $matchesProfileStatusService = FService::build('MatchesProfileStatusService');
        $statuses = $matchesProfileStatusService->getMenu();
        foreach ($profiles['items'] as &$profile) {
            $profile['status_str'] = $statuses[$profile['status']];
        }
        return $profiles;
    }


    /**
     *  Get a profile to show it
     *
     * @param integer $id   profile id
     * @return array
     */
    public function show(int $id): array
    {
        $profile = $this->model->get($id, ['with' => ['userFields', 'matchesComments']]);

        if (empty($profile)) {
            return [];
        }
        /** @var MatchesProfileStatusService $matchesProfileStatusService */
        $matchesProfileStatusService = FService::build('MatchesProfileStatusService');

        $statues = $matchesProfileStatusService->getMenu();
        $profile[0]['status_str'] = $profile[0]['status'] ? $statues[$profile[0]['status']] : '';


        $matches = $this->showMatches($profile[0]['matches_form_id'], $profile[0]);
        $userFields = $this->showMatches(MatchesForm::$PERSONAL_DATA_FORM_ID, $profile[0]['userFields']);
        $matchesOrder = array_combine(
            array_column($matches, 'ordering'),
            array_column($matches, 'db_code')
        );
        ksort($matchesOrder);
        $profile[0]['matches_order'] = $matchesOrder;
        return $profile;
    }

    /**
     * Show matches of one form and profile
     *
     * @param integer $formId
     * @param array $profile
     * @return array
     */
    private function showMatches(int $formId, array &$profile): array
    {
        /** @var MatchesOptions $matchesOptions */
        $matchesOptions = FModel::build('MatchesOptions');
        /** @var Matches $matches */
        $matches = FModel::build('Matches');
        $matches = $matches->get(
            1,
            [
                'column' => 'active',
                'with' => ['options'],
                'filter' => [
                    'needle' => $formId,
                    'columns' => 'matches_form_id'
                ]
            ]
        );

        $matches = array_combine(
            array_column($matches, 'db_code'),
            $matches
        );

        foreach (array_keys($profile) as $key) {
            if (isset($matches[$key])) {
                if ($matches[$key]['matches_form_id'] == $formId) {
                    $value = $profile[$key];

                    $match = [
                        'value' => $value,
                        'type' => $matches[$key]['match_type'],
                        'label' => $matches[$key]['label'],
                        'description' => $matches[$key]['description'],
                    ];
                    if (
                        in_array(
                            $matches[$key]['match_type'],
                            [
                                'MULTIPLE_CHOOSE',
                                'MULTIPLE_CHOOSE_OR',
                                'MENU',
                                'RADIO_BUTTON'
                            ]
                        )
                    ) {
                        $options = is_string($value) ? $matchesOptions->get(explode(',', $value)) : [];
                        $match['selected_options'] = array_combine(
                            array_column($options, 'id'),
                            array_column($options, 'value')
                        );
                        $selectedOptions = $match['selected_options'];
                        sort($selectedOptions);
                        $match['valuesStr'] = implode(',', $selectedOptions);

                        $options = $matchesOptions->get(
                            $matches[$key]['id'],
                            [
                                'column' => 'matches_id',
                                'order' => ['ordering' => 'asc']
                            ]
                        );
                        $match['options'] = array_combine(
                            array_column($options, 'id'),
                            array_column($options, 'value')
                        );
                    }
                    $profile['matches'][$key] = $match;
                }
            }
        }
        return $matches;
    }

    /**
     * Delete a profile
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id): void
    {
        $profile = $this->model->get($id);
        $userId = $profile[0]['user_id'];
        /** @var MatchesOptionValues $matchesOptionValues */
        $matchesOptionValues = FModel::build('MatchesOptionValues');
        $matchesOptionValues->delete($id, 'matches_profile_id');
        /** @var MatchesComments $mMatchesComments */
        $mMatchesComments = FModel::build('MatchesComments');
        $mMatchesComments->delete($id, 'matches_profile_id');
        $this->model->delete($id);
        /** @var MatchesUsers $mMatchesUsers */
        $mMatchesUsers = FModel::build('MatchesUsers');
        $mMatchesUsers->delete($userId);
        $this->model->delete($id);
    }

    /**
     * Delete all profiles conntected to one particular form (when a form is to be deleted)
     *
     * @param integer $formId
     * @return void
     */
    public function destroyProfilesInForm(int $formId)
    {
        $profiles = $this->model->get($formId, ['column' => 'matches_form_id']);
        foreach ($profiles as $profile) {
            $this->delete($profile['id']);
        }
    }

    /**
     * Add a new profile
     *
     * @param array $request  profile data an array
     * @return array
     */
    public function store(array $request): array
    {
        if (!isset($request['status'])) {
            $request['status'] = MatchesProfileStatusService::$inserted;
        }

        /** Split the profile and user params to separate arrays */
        /** @var MatchesUsers $mMatchesUsers */
        $mMatchesUsers = FModel::build('MatchesUsers');
        $userParams = [];
        $profileParams = [];
        $comments = [];
        foreach ($request as $key => $value) {
            if (str_starts_with($key, self::$matchesUsersExtension)) {
                $userParamsKey = $key;
                $userParams[$userParamsKey] = $value;
            } elseif (str_starts_with($key, self::$matchesCommentsExtension)) {
                $comments[$key] = $value;
            } else {
                $profileParams[$key] = $value;
            }
        }

        $userId = $mMatchesUsers->insert($userParams, false);

        $profileParams['user_id'] = $userId;
        if (!isset($profileParams['name'])) {
            $profileParams['name'] = $userParams['f0_naam'];
        } else {
            if (!$profileParams['name']) {
                $profileParams['name'] = $userParams['f0_naam'];
            }
        }

        // Note: first we insert profile without matches,
        // after having an id we update the matches to it
        $profileParams['id'] = $this->model->insert($profileParams);
        $this->updateMatchesOptions($profileParams);
        $this->updateMatchesComments($comments, $profileParams['id']);
        $this->model->update($profileParams, 'id', false);
        $profile = $this->show($profileParams['id']);

        $tags = $this->setTags(current($profile));
        $profile[0]['tags'] = $tags;
        $this->model->update(
            [
                'id' => current($profile)['id'],
                'tags' => $tags,
            ],
            'id',
            false
        );

        $this->sendAdminNotifications($profile);
        $this->sendApplicationWelcomeMail($profile);

        return $profile;
    }


    public function update(array $request): array
    {
        foreach (['matches_form_id', 'userFields', 'user_id', 'is_professional', 'matches_order'] as $columnNotToBeUpdated) {
            if (isset($request[$columnNotToBeUpdated])) {
                unset($request[$columnNotToBeUpdated]);
            }
        }

        /** @var MatchesProfileStatusService $matchesProfileStatusService */
        $matchesProfileStatusService = FService::build('MatchesProfileStatusService');

        if (!isset($request['status'])) {
            $request['status'] = $matchesProfileStatusService::$inserted;
        }

        $this->updateMatchesComments($request, $request['id']);
        $this->updateMatchesOptions($request);
        $this->model->update($request, 'id', false);

        $profile = $this->show($request['id']);
        $tags = $this->setTags(current($profile));
        $profile[0]['tags'] = $tags;
        $this->model->update(
            [
                'id' => current($profile)['id'],
                'tags' => $tags,
            ],
            'id',
            false
        );

        return $profile;
    }

    private function updateMatchesOptions(&$request): void
    {
        /** @var MatchesOptionValues $matchesOptionsValues */
        $matchesOptionsValues = FModel::build('MatchesOptionValues');

        foreach ($request as $key => &$value) {
            if (is_array($value)) {
                $matchesOptionsValues->updateOption($key, $request['id'], $value);
                sort($value);
                $value = implode(',', $value);
            }
        }
    }

    private function updateMatchesComments(&$comments, $profileId): void
    {
        /** @var MatchesComments $mMatchesComments */
        $mMatchesComments = FModel::build('MatchesComments');
        $currentComments = $mMatchesComments->get($profileId, ['column' => 'matches_profile_id']);
        $currentComments = array_combine(
            array_column($currentComments, 'db_code'),
            $currentComments
        );
        foreach ($comments as $key => $comment) {
            if (str_starts_with($key, self::$matchesCommentsExtension)) {
                $dbCode = str_replace(self::$matchesCommentsExtension, '', $key);
                if (isset($currentComments[$dbCode])) {
                    $mMatchesComments->update([
                        'id' => $currentComments[$dbCode]['id'],
                        'comment' => $comment,
                    ]);
                } else {
                    $mMatchesComments->insert(
                        [
                            'db_code' => $dbCode,
                            'matches_profile_id' => $profileId,
                            'comment' => $comment,
                        ]
                    );
                }
                unset($comments[$key]);
            }
        }
    }

    /**
     * Update the table matches_profile with the columns of matches.db_code
     *
     * @param string $env use 'test' if you create a model in test environment
     * @return void
     * @throws \Exception
     */
    public function updateMatchesColumns(string $env = ''): void
    {
        $this->updateColumns($this->model, $env);
    }


}
