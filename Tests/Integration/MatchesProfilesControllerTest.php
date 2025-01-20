<?php

namespace Integration;

require_once __DIR__ . '/../BaseTest.php';

use App\Enums\EMatchType;
use App\Factory\FModel;
use App\Factory\FService;
use App\Models\Matches;
use App\Models\MatchesProfile;
use App\Services\MatchesProfileStatusService;
use App\Plugins\Http\Response;
use App\Services\MatchesProfileService;
use Tests\BaseTest;
use Tests\Traits\THttpRequest;


class MatchesProfilesControllerTest extends BaseTest
{

    use THttpRequest;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIndex(): void
    {
        $apiToken = $this->createAdminUser()['apiToken'];

        $useCases = [
            'page8 ,pageSize 2' => [
                'queryString' => '&page_size=4&page=8&order=name',
                'expected' => [
                    'total' => 42,
                    'page' => '8',
                    'page_size' => '4',
                    'number_of_pages' => 11,
                    'items_count' => 4,
                    'first_profile_name' => 'Hugo van den  Broek',
                    'last_profile_name' => 'Feline Verhoeven'
                ]
            ],
            'last page,pageSize 5' => [
                'queryString' => '&page_size=5&page=8&order=name',
                'expected' => [
                    'total' => 42,
                    'page' => '8',
                    'page_size' => '5',
                    'number_of_pages' => 9,
                    'items_count' => 2,
                    'first_profile_name' => 'Bo Postma',
                    'last_profile_name' => 'Anna Dijkstra'
                ]
            ],
            'search,pageSize 5, laste page' => [
                'queryString' => '&page=1&page_size=5&search=der&order=name',
                'expected' => [
                    'total' => 7,
                    'page' => '1',
                    'page_size' => '5',
                    'number_of_pages' => 2,
                    'items_count' => 2,
                    'first_profile_name' => 'Lotte van der  Velden',
                    'last_profile_name' => 'Juna van der  Berg'
                ]
            ],
        ];

        /** @var MatchesProfile $matchesProfile */
        $matchesProfile = FModel::build('MatchesProfile');
        foreach ($this->getTestProfileNames() as $testProfileName) {
            $matchesProfile->insert([
                'name' => $testProfileName,
                'matches_form_id' => self::getTestFormId(),
                'status' => MatchesProfileStatusService::$inserted,
                'tags' => $testProfileName,
                'is_professional' => 1,
                'user_id' => $this->getTestUser(),
            ]);
        }

        foreach ($useCases as $caseName => $case) {
            $response = $this->sendRequest(
                'GET',
                '/api/matchesprofiles/1',
                [],
                null,
                $case['queryString'] . '&all=true&is_professional=' . MatchesProfile::$company,
                $apiToken,
            );

            $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);
            foreach ($case['expected'] as $expectedKey => $expected) {
                switch ($expectedKey) {
                    case 'first_profile_name':
                        $this->assertEquals(
                            $expected,
                            current($response['body']['data'])['name'],
                            'case : ' . $caseName
                        );
                        break;
                    case 'last_profile_name':
                        $this->assertEquals(
                            $expected,
                            end($response['body']['data'])['name'],
                            'case : ' . $caseName
                        );
                        break;
                    default:
                        $this->assertEquals(
                            $expected,
                            $response['body'][$expectedKey],
                            'case : ' . $caseName . ' , ' . $expectedKey
                        );
                }
            }
        }
    }


    public function testStoreAndUpdate()
    {
        $apiToken = $this->createAdminUser()['apiToken'];

        /** @var MatchesProfileService $matchesProfileService */
        $matchesProfileService = FService::build('MatchesProfileService');
        $matchesProfileService->updateMatchesColumns();


        $params = [
            MatchesProfileService::$matchesUsersExtension . 'naam' => 'test user eee',
            MatchesProfileService::$matchesUsersExtension . 'email' => 'testuser@eee.nl',
            'name' => 'new profile',
            'status' => MatchesProfileStatusService::$inserted,
            'matches_form_id' => self::getTestFormId(),
            'is_professional' => 1,
            'description' => 'test description',
            'f1_Yearsofexperience657162' => 12,
            'f1_programminglanguages657162' => [2, 3], // Java, c++
            'f1_Favouriteworkingday657162' => [6], // Friday
            'matches_comment_f1_Yearsofexperience657162' => 'test comment',
            'matches_comment_f1_Programminglanguages657162' => 'test comment Programminglanguages',
        ];


        $response = $this->sendRequest('POST', '/api/matchesprofiles', $params, self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);
        $this->assertEquals('Inserted', $response['body']['message']);
        $this->assertEquals('new profile', $response['body']['data'][0]['name']);
        $this->assertEquals('1', $response['body']['data'][0]['is_professional']);
        $this->assertEquals('test user eee', $response['body']['data'][0]['userFields'][MatchesProfileService::$matchesUsersExtension . 'naam']);
        $this->assertEquals(12, $response['body']['data'][0]['matches']['f1_Yearsofexperience657162']['value']);
        $this->assertEquals('2,3', $response['body']['data'][0]['matches']['f1_Programminglanguages657162']['value']);
        $this->assertEquals('f1_Programminglanguages657162', $response['body']['data'][0]['matchesComments'][1]['db_code']);
        $this->assertEquals('test comment Programminglanguages', $response['body']['data'][0]['matchesComments'][1]['comment']);
        $user_id = $response['body']['data'][0]['userFields']['id'];
        $this->assertEquals($user_id, $response['body']['data'][0]['user_id']);
        $profileId = $response['body']['data'][0]['id'];

        $params = [
            'id' => $profileId,
            'matches_form_id' => self::getTestFormId(),
            'name' => 'new profile 1',
            'status' => MatchesProfileStatusService::$inserted,
            'f1_Yearsofexperience657162' => 2,
            'f1_programminglanguages657162' => [1, 3], // php, c++
            'f1_Favouriteworkingday657162' => [5], // Tuesday
            'matches_comment_f1_Yearsofexperience657162' => 'test comment',
        ];

        $response = $response = $this->sendRequest(
            'PUT',
            '/api/matchesprofiles',
            $params,
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken
        );

        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);
        $this->assertEquals('Updated', $response['body']['message']);
        $this->assertEquals('new profile 1', $response['body']['data'][0]['name']);
        $this->assertEquals('f1_Yearsofexperience657162', $response['body']['data'][0]['matchesComments'][0]['db_code']);
        $this->assertEquals('test comment', $response['body']['data'][0]['matchesComments'][0]['comment']);

        $expected = [
            'f1_Yearsofexperience657162' =>
            [
                'value' => '2',
                'type' => 'BIGGER_THAN',
                'label' => 'Years of experience',
            ],
            'f1_Programminglanguages657162' =>
            [
                'value' => '1,3',
                'type' => 'MULTIPLE_CHOOSE',
                'label' => 'Programming languages',
                'selected_options' =>
                [
                    1 => 'php',
                    3 => 'c++',
                ],
                'valuesStr' => 'c++,php',
                'options' =>
                [
                    1 => 'php',
                    2 => 'java',
                    3 => 'c++',
                ],
            ],
            'f1_Favouriteworkingday657162' =>
            [
                'value' => '5',
                'type' => 'MENU',
                'label' => 'Favourite working day',
                'selected_options' =>
                [
                    5 => 'Tuesday',
                ],
                'valuesStr' => 'Tuesday',
                'options' =>
                [
                    4 => 'Monday',
                    5 => 'Tuesday',
                    6 => 'Friday',
                ],
            ],
        ];

        foreach ($expected as $key => $expectedItem) {
            if (!isset($expectedItem['description'])) {
                $expectedItem['description'] = null;
            }
            $this->assertEquals(
                $expectedItem,
                $response['body']['data'][0]['matches'][$key],
                'case  : ' . $key
            );
        }
    }


    public function testShow()
    {
        $apiToken = $this->createAdminUser()['apiToken'];

        /**
         * In this test we do the following
         * 1) We add new user and profile
         * 3) Check:
         *   a) Only matches of the current form are in the response
         *   b) Check the labels
         *
         *   .. later more ...
         */

        //
        // Add profile for the test user, the is tested in testStoreAndUpdate()
        //
        /** @var MatchesProfileService $matchesProfileService */
        $matchesProfileService = FService::build('MatchesProfileService');
        $matchesProfileService->updateMatchesColumns();
        $userId = $this->getTestUser();

        $params = [
            MatchesProfileService::$matchesUsersExtension . 'naam' => 'test user  1',
            MatchesProfileService::$matchesUsersExtension . 'email' => 'testuser1@dsufydisu.ck',
            'name' => 'new profile',
            'status' => MatchesProfileStatusService::$inserted,
            'matches_form_id' => self::getTestFormId(),
            'user_id' => $userId,
            'is_professional' => 1,
            'description' => 'test description',
        ];


        $response = $this->sendRequest(
            'POST',
            '/api/matchesprofiles',
            $params,
            self::$CONTENT_TYPE_JSON
        );
        $profileId = $response['body']['data'][0]['id'];

        //
        // Add other  form
        //
        $formParams = ['name' => 'test form 2'];
        $response = $this->sendRequest(
            'POST',
            '/api/matchesform',
            $formParams,
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken,
        );
        $newFormId = $response['body']['data'][0]['id'];
        // Update new form by adding matches to it
        $request = [
            'name' => 'test form 2',
            "matches" => [
                [
                    "active" => 1,
                    "label" => "New From 2 match 1",
                    "matches_form_id" => $newFormId,
                    'match_type' => EMatchType::$EQUAL,
                ],
                [
                    "active" => 1,
                    "label" => "New From 2 match 2",
                    "matches_form_id" => $newFormId,
                    'match_type' => EMatchType::$BIGGER_THAN
                ]
            ]
        ];

        $this->sendRequest(
            'PUT',
            '/api/matchesform/' . $newFormId,
            $request,
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken,
        );

        //
        // Get the profile  and test
        //
        $response = $this->sendRequest(
            'GET',
            '/api/matchesprofiles/show/' . $profileId,
            [],
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken,
        );

        // Check that we have correct matches
        /** @var Matches $mMatches */
        $mMatches = FModel::build('Matches');
        $formMatches = [
            'correct form' => [
                'formId' => self::getTestFormId(),
                'exists' => true
            ],
            'wrong form' => [
                'formId' => $newFormId,
                'exists' => false
            ]
        ];
        foreach ($formMatches as $case => $form) {
            $matchesToCheck = $mMatches->get($form['formId'], ['column' => 'matches_form_id']);
            $dbCodes = array_column($matchesToCheck, 'db_code');
            foreach ($dbCodes as $dbCode) {
                $this->assertEquals(
                    $form['exists'],
                    isset($response['body']['data'][0]['matches'][$dbCode]),
                    'case ' . $case . ' column ' . $dbCode
                );
            }
        }
    }

    /**
     * In this test we check that when an option of a match is deleted,
     * it is also delete from the profiles
     * Deleting an option from a match is tested in MatchesControllerTest.php
     *
     */
    public function testDeleteOption()
    {
        $apiToken = $this->createAdminUser()['apiToken'];

        // ---------------------------------------------------------------
        //
        // 1 create user and assign the options php, c++ as  programming languages
        //
        // ---------------------------------------------------------------
        /** @var Matches $matches */
        $matches = FModel::build('Matches');
        $programmingLanguagesMatch = current(
            $matches->get(
                'f1_Programminglanguages657162',
                [
                    'column' => 'db_code',
                    'with' => ['options']
                ]
            )
        );

        $matchOptions = array_combine(
            array_column($programmingLanguagesMatch['options'], 'value'),
            $programmingLanguagesMatch['options']
        );


        // ---------------------------------------------------------------
        //
        // 2 create user and assign the options php, c++ as  programming languages
        //
        // ---------------------------------------------------------------
        /** @var MatchesProfileService $matchesProfileService */
        $matchesProfileService = FService::build('MatchesProfileService');
        $matchesProfileService->updateMatchesColumns();
        $userId = $this->getTestUser();

        $params = [
            MatchesProfileService::$matchesUsersExtension . 'naam' => 'test user  1',
            MatchesProfileService::$matchesUsersExtension . 'email' => 'testuser1@dsufydisu.ck',
            'name' => 'new profile',
            'status' => MatchesProfileStatusService::$inserted,
            'matches_form_id' => self::getTestFormId(),
            'user_id' => $userId,
            'is_professional' => 1,
            'description' => 'test',
        ];

        $response = $this->sendRequest(
            'POST',
            '/api/matchesprofiles',
            $params,
            self::$CONTENT_TYPE_JSON
        );
        $profileId = $response['body']['data'][0]['id'];

        $params = [
            'id' => $profileId,
            'f1_programminglanguages657162' => [
                $matchOptions["php"]['id'],
                $matchOptions["c++"]['id'],
                $matchOptions["java"]['id'],
            ],
        ];

        $response = $response = $this->sendRequest(
            'PUT',
            '/api/matchesprofiles',
            $params,
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken,
        );

        // ---------------------------------------------------------------
        //
        // 3 delete match option c++
        //
        // ---------------------------------------------------------------

        $params = [
            'label' => 'Multiple choose 2',
            'id' => $programmingLanguagesMatch['id'],
            'options' =>
            [
                'php' => [
                    'id' => $matchOptions['php']['id'],
                    'value' => 'php',
                ],

                'java' => [
                    'id' => $matchOptions['java']['id'],
                    'value' => 'java',
                ],
            ]  // Option c++ is to be deleted, because it is not on the list
        ];

        $response = $this->sendRequest(
            'PUT',
            '/api/matches/',
            $params,
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken,
        );
        // Deleting option from match is tested in MatchesControllerTest.php

        // ---------------------------------------------------------------
        //
        // 2 delete match option c++
        //
        // ---------------------------------------------------------------
        $response = $this->sendRequest(
            'GET',
            '/api/matchesprofiles/show/' . $profileId,
            [],
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken,
        );
        $expected = implode(
            ',',
            [
                $matchOptions["php"]['id'],
                $matchOptions["java"]['id']
            ]
        );

        $this->assertEquals($expected, $response["body"]["data"][0]['f1_Programminglanguages657162']);
        //  dd($response);
    }

    public function testDestroy()
    {
        $apiToken = $this->createAdminUser()['apiToken'];

        /** @var MatchesProfile $matchesProfile */
        $matchesProfile = FModel::build('MatchesProfile');
        /** @var int $id */
        $id = 0; // make phpstan happy
        foreach ($this->getTestProfileNames() as $testProfileName) {
            $id = $matchesProfile->insert([
                'name' => $testProfileName,
                'status' => MatchesProfileStatusService::$inserted,
                'matches_form_id' => self::getTestFormId(),
                'is_professional' => 1,
                'user_id' => $this->getTestUser(),
            ]);
        }
        $response = $this->sendRequest(
            'DELETE',
            '/api/matchesprofiles/' . $id,
            [],
            null,
            '',
            $apiToken,
        );
        $this->assertEquals('Profile with id ' . $id . ' is deleted.', $response['body']['message']);
        $response = $this->sendRequest(
            'GET',
            '/api/matchesprofiles/show/' . $id,
            [],
            null,
            '',
            $apiToken,
        );
        $this->assertEquals('Item with id ' . $id . ' not found', $response['body']['message']);
    }

    private function getTestProfileNames(): array
    {
        return [
            'Milan Smeets',
            'James de  Wit',
            'Anna Dijkstra',
            'Ella Schipper',
            'Bo Postma',
            'Veerle van den  Bosch',
            'Stijn Brouwer',
            'Yuna Maas',
            'Noor Dijkstra',
            'Owen Bosman',
            'Dean Evers',
            'Mason de  Haan',
            'Julie Boer',
            'Eline de  Ruiter',
            'Yuna Boer',
            'Tijn van den  Heuvel',
            'Jill Smeets',
            'Hailey Brouwer',
            'Feline Verhoeven',
            'Loïs Hendriks',
            'Lotte van der  Velden',
            'Fleur de  Haan',
            'Max van der  Laan',
            'Nora van  Loon',
            'Quinn Vermeulen',
            'Otis Sanders',
            'Lev Janssen',
            'Job Dekker',
            'Ryan van der  Meulen',
            'Tessa Vos',
            'Juna van der  Berg',
            'Veerle Sanders',
            'Maya Dijkstra',
            'Hugo van den  Broek',
            'Nola van der  Velden',
            'Zayn de  Leeuw',
            'Noé de  Boer',
            'Sophie Koning',
            'Joep Dekker',
            'Elena Groen',
            'Tygo Visser',
            'Lizzy Kok',
        ];
    }
}
