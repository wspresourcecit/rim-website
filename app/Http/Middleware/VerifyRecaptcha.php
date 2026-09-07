<?php

namespace App\Http\Middleware;

use App\Services\v1\Backend\Setting\ExtensionAPIService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    public function __construct(private ExtensionAPIService $extensionAPIService)
    {
    }

    /**
     * Verify the Google reCAPTCHA token on public form submissions.
     *
     * No-ops when the "recaptcha" extension is inactive, so it is safe to
     * keep on the route while the feature is toggled off in the admin panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->extensionAPIService->verifyRecaptcha([
            'recaptcha_token' => $request->input('recaptcha_token'),
        ]);

        return $next($request);
    }
}
