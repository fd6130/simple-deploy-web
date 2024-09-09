<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::fallback(function ()
{
    return response()->json([
        'status' => 'fail',
        'http_code' => 404,
        'error_code' => null,
        'message' => 'Not Found'
    ], 404);
});

Route::get('/user', function (Request $request)
{
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])->group(function ()
{
    Route::group([
        'controller' => App\Http\Controllers\Api\AuthController::class,
        'prefix' => 'auth'
    ], function ()
    {
        Route::withoutMiddleware('auth:sanctum')->post('login', 'login');
        Route::post('logout', 'logout');
        Route::get('me', 'me');
        Route::put('me', 'updateMe');
        Route::patch('password', 'changePassword');
        Route::withoutMiddleware('auth:sanctum')->post('reset-password', 'requestResetPassword');
        Route::withoutMiddleware('auth:sanctum')->get('reset-password/{model}/check', 'checkResetPassword');
        Route::withoutMiddleware('auth:sanctum')->patch('reset-password/{model}/reset', 'processResetPassword');
    });
});
