<?php

namespace App\Traits;

/**
 *  Class to generate the API response
 */

use App\Plugins\Http\Response as Status;

trait TResponse
{
    /**
     * @var string
     */
    static public $Ok = 'Ok';

    /**
     * @var string
     */
    static public $NotFound = 'NotFound';

    public static $Unauthorized = 'Unauthorized';

    /**
     * @var string
     */
    static public $BadRequest = 'BadRequest';

    protected function  response(array $response, string $type = ''): array
    {

        if (!$this->isApiCall) {
            return $response;
        }

        switch ($type) {
            case self::$NotFound:
                (new Status\NotFound($response))->send();
                return [];
            case self::$BadRequest:
                (new Status\BadRequest($response))->send();
                return [];
            case self::$Unauthorized:
                (new Status\Unauthorized($response))->send();
                return [];
            default:
                (new Status\Ok($response))->send();
        }
        return [];
    }


    public function notAuthenticated(array $response) {
        $r= new Status\Unauthorized(
            $response
        );  
        $statusHeader = 'HTTP/1.1 ' . $r->getCode() . ' ' . $r->getMessage();
        $contentTypeHeader = 'Content-type: ' . $r->getContentType();
        header($statusHeader);
        header($contentTypeHeader);
        die( $r->getBody());
    }
}