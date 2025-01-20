<?php

/*
 * NOTE: The following files are also to be updated
 *
 * api_/config/configTest.php
 * api_/config/config.php
 *
 *
 */
$params =  [
    'db' => [
        'host' => 'x',
        'database' => 'x',
        'username' => 'x',
        'password' => 'x',
    ],
    'api_host' => '',  //  https://www.test.nl/  or http://test.test.nl/

    'admin' => [
        'passwordSalt' => 'Example',
        'api_key_expires' => 900, //seconds = 15 min
    ],
    'frontEndHost' => '',//https://aanmelden.test.nl/
    'mailData' => [
        'fromEmail' => 'info@test.nl',
        'fromName' => 'Demo site',
        'replyEmail' => 'info@test.nl',
        'replyName' => 'Demo site',
        'testEmail' => 'test@tantonius.com',
    ],
    'liveServer' => 'aanmelden.test.nl',
    'setLogStatemnts' => false,
];

require __DIR__ . '/authorizations/list.php';

return $params;