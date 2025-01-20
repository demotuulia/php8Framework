<?php

namespace App\Controllers;

/**
 * Controller to handle profiles of the users
 *
 */


use App\Models\MatchesForm;
use App\Services\MatchesProfileStatusService;
use App\Factory\FService;
use App\Services\MatchesProfileService;
use App\Services\MatchesService;

class MatchesProfilesController extends BaseController
{
    /**
     * @var MatchesProfileService
     */
    private MatchesProfileService $matchesProfileService;


    /**
     * construct
     *
     * @param array $request
     */
    public function __construct(array $request = [])
    {
        parent::__construct($request);
        $this->matchesProfileService = new MatchesProfileService();
    }

    /**
     * Index
     *
     * With this function has following options
     *  . Require a specufuc page with a speific size
     *  . Wilcard search, based on the created tags of each profile
     *  . Filter by the satatus
     *  . Define the ordering column (default created_at, desc)
     * 
     * See examples on Tests/Integration/MatchesProfilesControllerTest.php
     * 
     * @return array
     */
    public function index(): array
    {
        $paginate = [];
        if (isset($this->queryParams['page'])) {
            $paginate['current_page'] = $this->queryParams['page'];
        }

        if (isset($this->queryParams['page_size'])) {
            $paginate['page_size'] = $this->queryParams['page_size'];
        }

        $options = [
            'paginate' => $paginate,
            'order' => ['name' => 'asc']
        ];

        if (isset($this->queryParams['search'])) {
            $options['wildcard'] = [
                'needle' => $this->queryParams['search'],
                'columns' => 'tags',
            ];
        }

        if (isset($this->queryParams['status'])) {
            if (is_numeric($this->queryParams['status'])) {
                $options['filter'] = [
                    'needle' => $this->queryParams['status'],
                    'columns' => 'status',
                ];
            }
        }

        if (isset($this->queryParams['order'])) {
            $options['order'] = [
                $this->queryParams['order'] => 'desc'
            ];
        } else {
            $options['order'] = [
                'created_at' => 'desc'
            ];
        }

        $value = $this->requestUriParam;
        $options['column'] = 'matches_form_id';


        $profiles = $this->matchesProfileService->search($value, $options);

        /** @var MatchesProfileStatusService $matchesProfileStatusService */
        $matchesProfileStatusService = FService::build('MatchesProfileStatusService');
        return $this->response(
            $this->paginationResponse($profiles, ['statuses' => $matchesProfileStatusService->getMenu()])
        );
    }

    public function store(): array
    {
        $errors = $this->validate();
        if (!empty($errors)) {
            return $this->response(['errors' => $errors], self::$BadRequest);
        }

        $match = $this->matchesProfileService->store($this->request);
        return $this->response([
            'message' => 'Inserted',
            'data' => $match
        ]);
    }

    public function update(): array
    {
        $errors = $this->validate();
        if (!empty($errors)) {
            $this->response(['errors' => $errors], self::$BadRequest);
        }
        $match = $this->matchesProfileService->update($this->request);
        return $this->response(['message' => 'Updated', 'data' => $match]);
    }

    public function destroy()
    {
        $this->matchesProfileService->delete($this->requestUriParam);
        return $this->response(['message' => 'Profile with id ' . $this->requestUriParam . ' is deleted.',]);
    }


    public function show(int $id)
    {
        $profile = $this->matchesProfileService->show($id);

        if (empty($profile)) {
            return $this->response(
                ['message' => 'Item with id ' . $this->requestUriParam . ' not found'],
                self::$NotFound
            );
        }

        /** @var MatchesProfileStatusService $matchesProfileStatusService */
        $matchesProfileStatusService = FService::build('MatchesProfileStatusService');

        return $this->response([
            'message' => 'Item with id ' . $this->requestUriParam,
            'statuses' => $matchesProfileStatusService->getMenu(),
            'data' => $profile
        ]);
    }

    protected function getValidationRules(): array
    {
        $rules = [];



        if ($_SERVER["REQUEST_METHOD"] == 'PUT') {

            $rules['id'] = [
                'label' => 'profile id',
                'type' => self::$VALIDATE_INTEGER,
                'exists' => [
                    'model' => 'MatchesProfile',
                    'column' => 'id'
                ],
                'required' => true,
            ];
        }

        $formId = $this->request['matches_form_id'];
        /** @var MatchesService $matchesService */
        $matchesService = FService::build('MatchesService');
        $matches = $matchesService->requiredFields($formId);

        foreach ($matches as $match) {
            $rules[$match['db_code']] = [
                'label' => $match['label'],
                'required' => true,
            ];
        }

        $userFields = $matchesService->requiredFields(MatchesForm::$PERSONAL_DATA_FORM_ID);

        foreach ($userFields as $match) {
            $rules[$match['db_code']] = [
                'label' => $match['label'],
                'required' => true,
            ];
        }

        return $rules;
    }
}
