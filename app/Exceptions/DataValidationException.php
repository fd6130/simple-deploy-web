<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DataValidationException extends \Exception
{
    public function __construct(private ValidationException $exception)
    {
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report(Request $request)
    {
        Log::channel('validation')->debug('Validation error', [
            'request_url' => $request->fullUrl(),
            'query' => $request->query->all(),
            'body' => $request->request->all(),
            'auth' => Auth::user() ? ['id' => Auth::user()?->id, 'email' => Auth::user()?->email] : null,
            'message' => $this->exception->validator->getMessageBag()->getMessages(),
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
            'http_code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'error_code' => null,
            'message' => $this->exception->validator->getMessageBag()->getMessages(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
