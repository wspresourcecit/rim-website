<?php

namespace App\Http\Controllers\v1\Backend;

use App\Http\Controllers\Controller;
use App\Services\v1\Backend\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected DashboardService $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * Admin: aggregated counts and lead trends for the admin-panel dashboard.
     * The payload is scoped to the modules the authenticated admin can access.
     */
    public function index(): JsonResponse
    {
        $data = $this->service->overview(auth('api')->user());

        return successResponse($data, 'Dashboard overview retrieved successfully');
    }
}
