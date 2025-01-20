<?php

namespace App\Controllers;

/**
 * Manage the users and their authentication
 * 
 */

use App\Services\AdminUsersService;

class AdminUsersController extends BaseController
{
    private AdminUsersService $adminUsersService;

    public function __construct(array $request = [])
    {
        parent::__construct($request);
        $this->adminUsersService = new AdminUsersService();
    }

    public function index(): array
    {
        $users = $this->adminUsersService->get();

        return $this->response(
            [
                'count' => count($users),
                'data' => $users
            ]
        );
    }

    public function show(): array
    {
        $id = $this->requestUriParam;
        if (!$this->adminUsersService->checkShowAuthorization($id)) {
            return $this->paginationResponse(['errors' => ['Not authorized']], self::$Unauthorized);
        }

        $status = $this->adminUsersService->show($id);
        return $this->response(
            [
                'data' => $status
            ]
        );
    }

    /**
     * Insert user
     *
     * @return array
     */
    public function store(): array
    {
        $errors = $this->validate();
        if (!empty($errors)) {
            return $this->response(['errors' => $errors], self::$BadRequest);
        }

        $response = $this->adminUsersService->store($this->request);
        return $this->response(
            [
                'message' => 'User register successfully.',
                'data' => $response
            ]
        );
    }

    /**
     * Update 
     *
     * @return array
     */
    public function update(): array
    {
        $errors = $this->validate();
        if (!empty($errors)) {
            return $this->response(['errors' => $errors], self::$BadRequest);
        }
        $user = $this->adminUsersService->update($this->request);
        return $this->response(
            [
                'message' => 'update successfull.',
                'data' => $user
            ]
        );
    }


    /**
     * Login by password or by email link. This function also created the e-mail link if required
     *
     * @return array
     */
    public function login(): array
    {
        // login by email link
        if (isset($this->queryParams['emailToken'])) {
            $user = $this->adminUsersService->loginByToken($this->queryParams['emailToken']);
            if (!empty($user)) {
                return $this->response([
                    'message' => 'Email login successfully.',
                    'data' => $user,

                ]);
            } else {
                return $this->response(['errors' => ['Login incorrect']], self::$Unauthorized);
            }
        }

        // Genereate e-mail link
        if (isset($this->request['generateEmailToken'])) {
            $user = $this->adminUsersService->generateEmailLoginToken($this->request);
            if (!empty($user)) {
                return $this->response([
                    'message' => 'Email login hash created.',
                    'data' => $user,
                ]);
            } else {
                return $this->response(['errors' => ['Incorrect email']], self::$Unauthorized);
            }
        }

        //Login by password 
        $user = $this->adminUsersService->login($this->request);
        if (!empty($user)) {
            return $this->response([
                'message' => 'User login successfully.',
                'data' => $user,

            ]);
        }


        $this->response(['errors' => ['Login incorrect']], self::$Unauthorized);
        return [];
    }

    /**
     * Logout
     *
     * @return array
     */
    public function logout(): array
    {
        $user = $this->adminUsersService->logout($this->requestUriParam);
        if (!empty($user)) {
            return $this->response([
                'message' => 'User logged out successfully.'

            ]);
        }
        $this->response(['errors' => ['Logout incorrect']], self::$Unauthorized);
        return [];
    }

    /**
     * Destroy
     *
     * @return void
     */
    public function destroy()
    {
        $this->adminUsersService->delete($this->requestUriParam);
        return $this->response(['message' => 'User with id ' . $this->requestUriParam . ' is deleted.',]);
    }

    /**
     * @inheritdoc
     */
    protected function getValidationRules(): array
    {
        $rules = [];
        $rules = [
            'name' => [
                'label' => 'naam',
                'type' => self::$VALIDATE_STRING,
                'required' => true,
            ],
            'email' => [
                'label' => 'e-mail',
                'type' => self::$VALIDATE_EMAIL,
                'unique' => [
                    'model' => 'AdminUsers',
                    'column' => 'email'
                ],
                'required' => true,
            ],
        ];

        $rules['new_password'] = [
            'label' => 'nieuwe wachtwoord',
            'type' => self::$VALIDATE_PASSWORD,
            'required' => false,

        ];


        if ($_SERVER["REQUEST_METHOD"] == 'POST') {

            $rules['admin_role_id'] = [
                'label' => 'role',
                'type' => self::$VALIDATE_INTEGER,
                'required' => true,
            ];
            $rules['password'] = [
                'label' => 'wachtwoord',
                'type' => self::$VALIDATE_PASSWORD,
                'unique' => [
                    'model' => 'AdminUsers',
                    'column' => 'email'
                ],
                'required' => true,
            ];
        }
        return $rules;
    }
}