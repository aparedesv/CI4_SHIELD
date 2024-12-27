<?php

namespace App\Helpers;

class ResponseHelper {

    public static function getResponse($status, $message, $error, $data = [])
    {
        return [
            'status' => $status,
            'message' => $message,
            'error' => $error,
            'data' => $data
        ];
    }
}