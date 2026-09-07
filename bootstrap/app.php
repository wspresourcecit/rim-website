<?php

use App\Exceptions\ExceptionHandler;
use App\Exceptions\WebExceptionHandler;
use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\VerifyRecaptcha;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => PermissionMiddleware::class,
            'admin' => AdminAuthMiddleware::class,
            'verify.recaptcha' => VerifyRecaptcha::class,
        ]);

        // Public website: gate every page behind the maintenance_mode flag
        // on the website_assets row. API routes are unaffected so the admin
        // panel can still toggle it back off.
        $middleware->appendToGroup('web', MaintenanceMode::class);
        // This app has no web-based login route, only the API guard.
        // Without this, an unauthenticated api/* request crashes with
        // "Route [login] not defined" instead of a clean 401 JSON response.
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $exception, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ExceptionHandler::handle($exception, $request);
            }

            // Web (HTML) requests: show a branded error page in production.
            // In debug mode, fall through to Laravel's detailed error page.
            if (! config('app.debug')) {
                return WebExceptionHandler::handle($exception, $request);
            }
        });
    })->create();
