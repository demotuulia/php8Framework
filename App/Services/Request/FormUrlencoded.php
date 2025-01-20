<?php

namespace App\Services\Request;

/**
 * Read the request of the format : multipart/form-data
 */
class FormUrlencoded implements IRequest
{
    /**
     * @inheritdoc
     */
    public function getRequest(): array
    {
        parse_str(file_get_contents("php://input"), $queryArray);
        return $queryArray;
    }
}