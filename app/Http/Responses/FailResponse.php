<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FailResponse extends JsonResponse
{
    public function __construct(
        $data = null,
        $message = '',
        $status = Response::HTTP_BAD_REQUEST,
        $headers = [],
        $options = 0,
        array|string|null $trace = null
    )
    {
        $data['result']  = false;
        $data['message'] = $message;

        if (env('APP_DEBUG') && $trace) {
            $data['trace'] = $trace;
        }

        parent::__construct($data, $status, $headers, $options);
    }
}