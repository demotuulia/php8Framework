<?php

namespace Tests\Traits;

/**
 * A class to send test requests and return the response
 *
 */
trait THttpRequest
{

    /**
     * @var string
     */
    public static $CONTENT_TYPE_JSON = 'application/json';
    /**
     * @var string
     */
    public static $CONTENT_TYPE_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    /**
     * @var string
     */
    public static $CONTENT_TYPE_FORM = 'multipart/form-data';

    protected function sendRequest(
        string $method,
        string $uri,
        array  $body = [],
        string $contentType = null,
        string $queryString = '', // query params starting by &
        string $authorization = null,
    ): array
    {
        $config = require __DIR__ . '/../../config/configTest.php';
        $curl = curl_init();;

        $postFields = [];
        switch ($contentType) {
            case self::$CONTENT_TYPE_WWW_FORM_URLENCODED :
                $postFields = http_build_query($body);
                break;
            case self::$CONTENT_TYPE_JSON :
                $postFields = json_encode($body);
                break;
            case self::$CONTENT_TYPE_FORM :
                $postFields = $body;
                break;
        };

        $headers =  array(
            'Content-Type: ' . $contentType
        );
        if ($authorization) {
          $headers[] = 'Authorization: Bearer ' . $authorization;
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $config['api_host'] . $uri . '?env=test' . $queryString,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        return [
            'status' => $info['http_code'],
            'body' => json_decode($response, true)
        ];
    }
}