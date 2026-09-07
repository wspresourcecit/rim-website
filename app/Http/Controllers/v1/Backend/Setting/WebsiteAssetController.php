<?php

namespace App\Http\Controllers\v1\Backend\Setting;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\v1\Backend\Setting\WebsiteAssetRequest;
use App\Http\Resources\v1\Backend\Setting\WebsiteAssetResource;
use App\Services\v1\Backend\Setting\WebsiteAssetService;

class WebsiteAssetController extends Controller
{
    protected $service;

    public function __construct(WebsiteAssetService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        try {
            $websiteAsset = $this->service->getWebsiteAsset();
            return successResponse($websiteAsset ? new WebsiteAssetResource($websiteAsset) : []);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function store(WebsiteAssetRequest $request): JsonResponse
    {
        try {
            $this->service->updateWebsiteAsset($request->validated());
            return successResponse([], 'Updated Successfully.');
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function websiteAssets(): JsonResponse
    {
        try {
            $websiteAsset = $this->service->getWebsiteAssets();
            return successResponse($websiteAsset ? new WebsiteAssetResource($websiteAsset) : []);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
