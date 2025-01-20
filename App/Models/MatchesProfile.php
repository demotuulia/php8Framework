<?php

namespace App\Models;

use App\Services\MatchesProfileStatusService;
use App\Factory\FModel;
use App\Factory\FService;

class MatchesProfile extends BaseModel
{
    /** @var string */
    protected string $table = 'matches_profile';

    // Definitions om some constants
    /** @var int string */
    public static int  $company = 1;

    /** @var int string */
    public static $professional = 0;

    // columns  
    /** @var int */
    protected int $id;

    /** @var int */
    protected int $matchesFormId;

    /** @var int */
    protected int $status;

    /** @var int */
    protected int $userId;

    /** @var bool */
    protected bool $isProfessional;

    /** @var string */
    protected string $name;

    /** @var string */
    protected string $description;

    /** @var string */
    protected string  $adminComment;

    /** @var string */
    protected string  $tags;

    /** @var string */
    protected string  $createdAt;

    /** @var string */
    protected string  $updatedAt;

    /**
     * Define a many to one relation between the table matches_profiles and matches_users
     * 
     * (One user may have many profiles)
     *
     * @param integer $id user id
     * @return array
     */
    public function user(int $id): array
    {
        return $this->ManyToOne($id, 'matches_id', FModel::build('MatchesUsers'));
    }


    /**
     * get profile(s)
     *
     * @param null|int $id             
     * @param array $options  
     * @return array
     */
    public function get($value = null, array $options = []): array
    {
        /** @var MatchesProfileStatusService $matchesProfileStatusService */
        $matchesProfileStatusService = FService::build('MatchesProfileStatusService');

        $profiles = parent::get($value, $options);
        // Get status as string if required
        if (isset($options['witStatusString'])) {
            $statuses = $matchesProfileStatusService->getMenu();
            foreach ($profiles as &$profile) {
                if (!is_null($profile['status'])) {
                    $profile['status_str'] = $statuses[$profile['status']];
                }
            }
        }
        return $profiles;
    }
}
