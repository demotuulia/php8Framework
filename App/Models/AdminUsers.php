<?php

namespace App\Models;


class AdminUsers extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'admin_users';


    /** @var string */
    protected int $id;
    /** @var string */
    protected int $adminRoleId;
    /** @var string */
    protected string $name;
    /** @var string */
    protected string $email;
    /** @var string */
    protected string $password;
    /** @var string */
    protected string $apiToken;
    /** @var string */
    protected int $apiTokenExpires;
    /** @var string */
    protected string $createdAt;
    /** @var string */
    protected string $updatedAt;
    /** @var string */
    /** @var string */
    protected string $emailLoginHash;
    /** @var string */
    protected int $emailLoginHashExpires;
}
