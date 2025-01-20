<?php
//////////////////////////////////////////////////////////////
// quest pages
//////////////////////////////////////////////////////////////

$params['guestPages'] = [
    'POST' => [],
    'PUT' => [],
    'GET' => [],
    'DELETE' => [],
];


$params['guestPages']['GET'][] = '/api/test';
$params['guestPages']['GET'][] = '/api';


$params['guestPages']['POST'][] = '/api/matchesprofiles';


$params['guestPages']['GET'][] = '/api/matchesform/(\d+)';
$params['guestPages']['GET'][] = '/api/matchesform';

$params['guestPages']['POST'][] = '/api/admin/users';
$params['guestPages']['POST'][] = '/api/admin/users/login';
$params['guestPages']['POST'][] = '/api/admin/users/logout/(\d+)';


$params['guestPages']['GET'][] = '/api/content/texts/show';

//////////////////////////////////////////////////////////////
// applicationManagerPages
//////////////////////////////////////////////////////////////

$params['applicationManagerPages'] = [
    'POST' => [],
    'PUT' => [],
    'GET' => [],
    'DELETE' => [],
];


$params['applicationManagerPages']['GET'][] = '/api/test';
$params['applicationManagerPages']['GET'][] = '/api';

$params['applicationManagerPages']['GET'][] = '/api/admin/dashboard';


$params['applicationManagerPages']['GET'][] = '/api/admin/users/(\d+)';
$params['applicationManagerPages']['PUT'][] = '/api/admin/users';

$params['applicationManagerPages']['POST'][] = '/api/matchesprofiles';
$params['applicationManagerPages']['GET'][] = '/api/matchesprofiles';
$params['applicationManagerPages']['DELETE'][] = '/api/matchesprofiles';

$params['applicationManagerPages']['GET'][] = '/api/matchesform/(\d+)';
$params['applicationManagerPages']['GET'][] = '/api/matchesform';

$params['applicationManagerPages']['POST'][] = '/api/admin/users';
$params['applicationManagerPages']['POST'][] = '/api/admin/users/login';
$params['applicationManagerPages']['POST'][] = '/api/admin/users/logout/(\d+)';

$params['applicationManagerPages']['GET'][] = '/api/content/texts/show';

