<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Application;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\DataValidationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware)
    {
        // remove empty string to null middleware
        $middleware->remove([
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class
        ]);

        // register your middleware here
        // $middleware->alias([]);
    })
    ->withExceptions(function (Exceptions $exceptions)
    {
        // add this part to every exception when occurs
        $exceptions->context(fn() => [
            'request_url' => request()->fullUrl(),
            'query' => request()->query->all(),
            'body' => request()->request->all(),
            'auth' => Auth::user() ? ['id' => Auth::user()?->id, 'email' => Auth::user()?->email] : null,
        ]);

        // use customize exception for validation exception
        $exceptions->render(function (ValidationException $exception)
        {
            throw new DataValidationException($exception);
        });

        // customize the spatie UnauthorizedException json response
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request)
        {
            throw new UnauthorizedException();
        });
    })->create();
