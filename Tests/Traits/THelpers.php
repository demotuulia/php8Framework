<?php

namespace Tests\Traits;

use App\Enums\EAdminRoles;
use App\Factory\FModel;
use App\Models\MatchesForm;
use App\Models\MatchesUsers;
use App\Services\MatchesProfileService;

trait THelpers
{

    public static string $adminUserEmail = 'adminTest@test.nl';
    public static string $adminUserPassword = '1232ddf3Sw';

    protected function getTestUser(string $name = ''): int
    {
        /** @var MatchesUsers $mUser */
        $mUser = FModel::build('MatchesUsers');
        $username = $name = !''
            ? strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $name))
            : 'testuser';
        return $mUser->insert(
            [
                MatchesProfileService::$matchesUsersExtension . 'naam' => $name != '' ? $name : 'testuser' . uniqid(),
                MatchesProfileService::$matchesUsersExtension . 'email' => $username . uniqid() . '@test.nl'
            ],
            false
        );
    }


    public static function getTestFormId(): int
    {
        $mMatchesForm = FModel::build('MatchesForm');
        /** @var MatchesForm $mMatchesForm */
        $f = $mMatchesForm->get('test', ['column' => 'name']);
        return current($f)['id'];
    }

    public function createAdminUser(bool $apiTokenOnly = true): array
    {
        // create
        $params = [
            'name' => 'adminTest',
            'email' => self::$adminUserEmail,
            'password' => self::$adminUserPassword,
            'admin_role_id' => EAdminRoles::$ADMIN,
        ];
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users',
            $params, self::$CONTENT_TYPE_JSON);

        //login
        $response  = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            [
                'email' => self::$adminUserEmail,
                'password' => self::$adminUserPassword,
            ],
            self::$CONTENT_TYPE_JSON);

        if ($apiTokenOnly) {
            return ['apiToken' => $response['body']['data'][0]['api_token']];
        } else {
            return $response;
        }
    }

}