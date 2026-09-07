<?php

namespace App\Http\Controllers\v1\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\PrivacyPolicyRequest;
use App\Http\Resources\v1\Backend\PrivacyPolicyResource;
use App\Models\PrivacyPolicy;
use App\Services\v1\Backend\PrivacyPolicyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PrivacyPolicyController extends Controller
{
    protected PrivacyPolicyService $service;

    public function __construct(PrivacyPolicyService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection|JsonResponse
    {
        $privacyPolicies = $this->service->getPrivacyPolicies($request->all());
        return PrivacyPolicyResource::collection($privacyPolicies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrivacyPolicyRequest $request): JsonResponse
    {
        $privacyPolicy = $this->service->storePrivacyPolicy($request->validated());
        return successResponse(new PrivacyPolicyResource($privacyPolicy), 'Privacy policy created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(PrivacyPolicy $privacyPolicy): JsonResponse
    {
        $privacyPolicy = $this->service->getPrivacyPolicy($privacyPolicy);
        return successResponse(new PrivacyPolicyResource($privacyPolicy), 'Privacy policy retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PrivacyPolicyRequest $request, PrivacyPolicy $privacyPolicy): JsonResponse
    {
        $privacyPolicy = $this->service->updatePrivacyPolicy($request->validated(), $privacyPolicy);
        return successResponse(new PrivacyPolicyResource($privacyPolicy), 'Privacy policy updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrivacyPolicy $privacyPolicy): JsonResponse
    {
        $this->service->deletePrivacyPolicy($privacyPolicy);
        return successResponse(new PrivacyPolicyResource($privacyPolicy), 'Privacy policy deleted successfully');
    }
}
