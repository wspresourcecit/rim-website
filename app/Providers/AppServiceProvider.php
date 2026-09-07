<?php

namespace App\Providers;

use App\Services\v1\Backend\BranchService;
use App\Services\v1\Backend\CourseService;
use App\Services\v1\Backend\FreeCourseService;
use App\Services\v1\Backend\PaymentMethodService;
use App\Services\v1\Backend\Setting\ExtensionService;
use App\Services\v1\Backend\Setting\WebsiteAssetService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensCan([
            'admin' => 'Admin access',
        ]);

        $this->configureRateLimiting();
       
    
        $settings = (new WebsiteAssetService())->getWebsiteAsset();

        $extensionService = new ExtensionService();

        View::share([
            'settings' => $settings,
            'googleAnalytics' => $extensionService->getCredentials('google_analytics'),
            'metaPixel' => $extensionService->getCredentials('meta_pixel'),
            'recaptcha' => $extensionService->getCredentials('recaptcha'),
        ]);
    }

    /**
     * Named rate limiters for the API. Referenced as `throttle:<name>` on routes.
     */
    protected function configureRateLimiting(): void
    {
        // Login: throttle per email+IP to blunt credential stuffing, with a
        // looser per-IP ceiling as a backstop against email enumeration.
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return [
                Limit::perMinute(5)->by($email . '|' . $request->ip()),
                Limit::perMinute(20)->by($request->ip()),
            ];
        });

        // Authenticated API traffic: per-token/user, falling back to IP.
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by(
                optional($request->user())->id ? 'user:' . $request->user()->id : 'ip:' . $request->ip()
            );
        });

        // Unauthenticated public endpoints (lead capture, asset fetch).
        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });
    }
}
