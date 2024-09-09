<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UnauthorizedException extends \Exception
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
            'http_code' => JsonResponse::HTTP_FORBIDDEN,
            'error_code' => null,
            'message' => 'You do not have the required authorization.',
        ], JsonResponse::HTTP_FORBIDDEN);
    }
}
