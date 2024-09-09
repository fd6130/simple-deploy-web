<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BadRequestException extends \Exception
{
    public function __construct(string $message = 'Bad Request')
    {
        $this->message = $message;
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report(Request $request)
    {
        Log::debug($this->message, [
            'request_url' => $request->fullUrl(),
            'query' => $request->query->all(),
            'body' => $request->request->all(),
            'auth' => Auth::user() ? ['id' => Auth::user()?->id, 'email' => Auth::user()?->email] : null,
        ]);

        return true;
    }

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
            'http_code' => JsonResponse::HTTP_BAD_REQUEST,
            'error_code' => null,
            'message' => $this->message,
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}
