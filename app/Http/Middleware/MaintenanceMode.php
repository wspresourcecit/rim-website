<?php

namespace App\Http\Middleware;

use App\Services\v1\Backend\Setting\WebsiteAssetService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MaintenanceMode
{
    public function __construct(private WebsiteAssetService $websiteAssetService)
    {
    }

    /**
     * Show a branded maintenance page for the public website when the
     * `maintenance_mode` flag is set on the website_assets row
     * (Admin panel > Website Asset). The custom `maintenance_message` is
     * rendered on that page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Never block the health check.
        if ($request->is('up')) {
            return $next($request);
        }

        try {
            $settings = $this->websiteAssetService->getWebsiteAsset();
        } catch (Throwable $e) {
            // If settings can't be read, don't take the whole site down.
            return $next($request);
        }

        if ($settings && $settings->maintenance_mode) {
            $message = trim((string) $settings->maintenance_message)
                ?: 'রক্ষণাবেক্ষণের কাজ চলছে। আমরা খুব শিগগিরই ফিরে আসছি — একটু পর আবার চেষ্টা করুন।';

            return response()
                ->view('errors.maintenance', ['maintenanceMessage' => $message], Response::HTTP_SERVICE_UNAVAILABLE)
                ->header('Retry-After', '3600');
        }

        return $next($request);
    }
}
