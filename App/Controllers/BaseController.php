<?php

namespace App\Controllers;
/**
 * Base controller
 */

use App\Plugins\Di\Injectable;
use App\Traits\TAuthenticate;
use App\Traits\TRequest;
use App\Traits\TResponse;
use App\Traits\TValidate;

/**
 * @property App\Plugins\Db\Db $db
 */
abstract class BaseController extends Injectable
{
    use TAuthenticate;
    use TRequest;
    use TResponse;
    use TValidate;

    /**
     * @var array
     */
    protected $request;

    /**
     * @var array
     */
    protected $queryParams;

    protected string $requestUriParam;

    /** @var bool */
    protected $isApiCall;

    /**
     * construct
     * 
     * @param null|array $request
     * @throws \Exception
     */
    public function __construct($request = [])
    {
        if (!self::authenticate()) {

            $this->notAuthenticated(
                ['errors' => ['Not Authorized'], self::$Unauthorized]
            );
        }

        $this->isApiCall = empty($request);
        $this->request = empty($request) ? $this->getRequest() : $request;
        $this->queryParams = !empty($request) ? $request : $this->getQueryParams();
        $this->requestUriParam = $this->getUriParam();
    }

    /**
     * Get validation rules
     * 
     * These are defined by the controllers
     *
     * @return array
     */
    protected abstract function getValidationRules(): array;

    /**
     * Response for the index actions having the pagination
     *
     * @param array $paginatedItems
     * @param array $extras
     * @return array
     */
    protected function paginationResponse(array $paginatedItems, array $extras =[]): array
    {
        $response = $paginatedItems;
        unset ($response['items']);
        $response['data'] = $paginatedItems['items'];

        return array_merge(
            $response,
            $extras
        );
    }
}
