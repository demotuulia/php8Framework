<?php

namespace App\Services\Request;

/**
 * Interface for the request types
 */
interface IRequest
{
    /**
     * A get the request of the given type.
     *
     * @return array
     */
    public function getRequest(): array;
}