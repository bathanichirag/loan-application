<?php

namespace App\Helpers;

class ResponseHelper
{

    /**
     *
     * @param type $message
     * @param type $code
     * @return type
     */
    public static function notFoundMessage($message, $code)
    {
        $response = [
            'code' => $code,
            'message' => $message,
        ];
        return response()->json($response, $response['code']);
    }


    /**
     *
     * @param type $code
     * @param type $message
     * @param type $count
     * @param type $payload
     * @return type
     */
    public static function successfulMessage($code, $message, $count, $payload)
    {
        $response = [
            'code' => $code,
            'message' => $message,
            'count' => $count,
            'data' => $payload,
        ];

        return response()->json($response, $response['code']);
    }


}
