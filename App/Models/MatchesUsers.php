<?php

namespace App\Models;

use App\Factory\FModel;

class MatchesUsers extends BaseModel
{

    protected $table = 'matches_users';

    /* @var int */
    private int $id;
    /* @var string */
    private string $name;
    /* @var string */
    private string $email;
    /* @var string */
    private string $createdAt;
    /* @var string */
    private string $updatedAt;

    /**
     * Create one to many relations with th a user and its profile(s)
     * 
     * @param int $id    profile id
     * @return array<MatchesProfile>
     */
    public function profiles(int $id): array
    {
        return $this->oneToMany($id, 'id', FModel::build('MatchesProfile'));
    }
}