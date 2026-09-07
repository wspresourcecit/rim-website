<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Renders branded HTML error pages for web (non-API) requests in production.
 * API requests are handled separately by {@see ExceptionHandler}. In debug
 * mode this is bypassed so Laravel's detailed error page still shows.
 *
 * Reporting/logging is left to Laravel's default handler, which still runs
 * before this renderer.
 */
class WebExceptionHandler
{
    public static function handle(Throwable $exception, Request $request): Response
    {
        $status = self::statusFor($exception);

        $view = view()->exists("errors.{$status}") ? "errors.{$status}" : 'errors.500';

        return response()->view($view, ['exception' => $exception], $status);
    }

    private static function statusFor(Throwable $exception): int
    {
        return match (true) {
            $exception instanceof ModelNotFoundException => 404,
            $exception instanceof AuthorizationException => 403,
            $exception instanceof TokenMismatchException => 419,
            $exception instanceof ThrottleRequestsException => 429,
            $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
            default => 500,
        };
    }
}
