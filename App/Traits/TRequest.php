<?php

namespace App\Traits;

/**
 * This trait reads the coming request parameters  and returns the results as array
 */

use App\Factory\FRequest;
use App\Services\Request\IRequest;

trait TRequest
{
    /**
     * Read the request
     * @return array
     * @throws \Exception
     */
    private function getRequest(): array
    {
        if (!isset($_SERVER['CONTENT_TYPE'])) {
            return [];
        }

        /** @var IRequest $request */
        $request = FRequest::build($_SERVER['CONTENT_TYPE']);
        return $request->getRequest();
    }

    /**
     * Get the query parameters of the request
     *
     * @return array
     */
    private function getQueryParams(): array
    {
        return $_GET;
    }

    /**
     * Get the uri param form the uri
     *
     * xxx/yyy/2  => 2
     * xxx/yyy/2?env=test  => 2
     *
     */
    private function getUriParam(): string
    {
        $parts = explode('/', $_SERVER["REQUEST_URI"]);
        $param = end($parts);
        $parts = explode('?', $param);
        $param = current($parts);
        return $param;
    }
}
