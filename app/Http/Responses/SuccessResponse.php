<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SuccessResponse extends JsonResponse
{
    public function __construct($data = null, $message = '', $status = Response::HTTP_OK, $headers = [], $options = 0)
    {
        $data['result']  = true;
        $data['message'] = $message;

        parent::__construct($data, $status, $headers, $options);
    }
}