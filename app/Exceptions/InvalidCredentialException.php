<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvalidCredentialException extends \Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render(Request $request)
    {
        return response()->json([
            'status' => 'fail',
            'http_code' => JsonResponse::HTTP_UNAUTHORIZED,
            'error_code' => null,
            'message' => 'Invalid Credentials',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
