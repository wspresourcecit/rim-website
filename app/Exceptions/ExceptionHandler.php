<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHandler
{
   public static function handle(Throwable $exception, Request $request)
   {
      if ($exception instanceof ApiException) {
         return errorResponse($exception->getMessage(), $exception->getStatus());
      }

      if ($exception instanceof ValidationException) {
         return errorResponse($exception->getMessage(), 422, ['errors' => $exception->errors()]);
      }

      // if ($exception instanceof Exception) {
      //    return errorResponse($exception->getMessage(), $exception->getCode());
      // }

      if ($exception instanceof ModelNotFoundException) {
         return errorResponse("Data not found.", 404);
      }

      if ($exception instanceof NotFoundHttpException) {
         return errorResponse("Resource not found.", 404);
      }

      if ($exception instanceof NotFoundException) {
         return errorResponse("Resource not found.", 404);
      }

      if ($exception instanceof AuthenticationException) {
         return errorResponse("Unauthenticated", 401);
      }

      // if ($exception instanceof Exception) {
      //    if ($exception->getCode()) {
      //       return errorResponse($exception->getMessage(), $exception->getCode());
      //    }
      // }

      $exceptionClass = get_class($exception);
      $location = $exception->getFile() . ':' . $exception->getLine();

      // Full detail goes to the logs only — never to the HTTP response.
      Log::error($exception->getMessage(), [
         'exception' => $exceptionClass,
         'location'  => $location,
         'url'       => $request->fullUrl(),
         'method'    => $request->method(),
         'trace'     => $exception->getTraceAsString(),
      ]);

      if (config('app.debug')) {
         // Local debugging: expose the message + where it happened, but not a
         // full stack trace or request internals.
         return errorResponse($exception->getMessage(), 500, [
            'exception' => $exceptionClass,
            'location'  => $location,
         ]);
      }

      try {
         Log::channel('slack')->error(sprintf(
            "*Exception:* %s\n*Message:* %s\n*File:* %s\n*Request:* %s %s",
            $exceptionClass,
            $exception->getMessage(),
            $location,
            $request->method(),
            $request->fullUrl()
         ));
      } catch (Throwable $loggingFailure) {
         // Never let a logging transport failure replace the real error.
      }

      return errorResponse('Something went wrong', 500);
   }
}
