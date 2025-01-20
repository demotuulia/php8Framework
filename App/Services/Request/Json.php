<?php

namespace App\Services\Request;

/**
 * Read the request of the format : application/json
 */
class Json implements IRequest
{
    public function getRequest(): array
    {
        $request = json_decode(file_get_contents("php://input"), true);
        return $request ?? [];
    }
}