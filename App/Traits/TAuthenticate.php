<?php

namespace App\Traits;

/**
 * 
 * Class to manage the authentication
 * 
 */

use App\Enums\EAdminRoles;
use App\Factory\FService;
use App\Services\AdminUsersService;
use App\Helpers\HConfig;

trait TAuthenticate
{
    public static function authenticate(): bool
    {
        if (self::loginRequired()) {
            return self::checkApiToken();
        }
        return true;
    }

    private static function loginRequired(): bool
    {
        return self::pageAllowed(HConfig::getConfig('guestPages'));
    }

    /**
     * Check if the current user is authorized to use current page
     *
     * @param array $allowedPages
     * @return boolean
     */
    private static function pageAllowed($allowedPages): bool
    {
        global $router;
        $method = $router->getRequestMethod();
        $allowed = true;
        foreach ($allowedPages[$method] as $uriPattern) {
            $uri = $router->getCurrentUri();
            if (self::patternMatches($uriPattern, $uri)) {
                $allowed = false;
            }
        }
        return $allowed;
    }


    /**
     * Check Api Token and the authentication
     *
     * @return boolean
     */
    private static function checkApiToken(): bool
    {
        $user = self::getUserByApiToken();
        if (empty($user)) {
            return false;
        }
        if ($user[0]['admin_role_id'] == EAdminRoles::$APPLICATON_MANAGER) {
            return !(self::pageAllowed(HConfig::getConfig('applicationManagerPages')));
        }
        // admin can all
        return  true;
    }

    /**
     * get User By Api Token sent in the request  header
     *
     * @return array
     */
    private static function getUserByApiToken(): array
    {
        global $router;
        if (!isset($router->getRequestHeaders()['Authorization'])) {
            return [];
        }

        $apiToken = str_replace('Bearer ', '', $router->getRequestHeaders()['Authorization']);
        /** @var AdminUsersService $adminUsersService */
        $adminUsersService = FService::build('AdminUsersService');
        return $adminUsersService->checkAdminApiToken($apiToken);
    }


    /**
     * Check if current URI matches with the action pattern
     *
     * @param string $pattern
     * @param string $uri
     * @return boolean
     */
    private static function patternMatches($pattern, $uri): bool
    {
        $matches = [];
        // Replace all curly braces matches {} into word patterns (like Laravel)
        $pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $pattern);

        // we may have a match!
        return boolval(
            preg_match_all(
                '#^' . $pattern . '$#',
                $uri,
                $matches,
                PREG_OFFSET_CAPTURE
            )
        );
    }
}
