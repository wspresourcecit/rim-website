<?php

namespace App\Http\Controllers\v1\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\PartnerCompanyRequest;
use App\Http\Resources\v1\Backend\PartnerCompanyResource;
use App\Models\PartnerCompany;
use App\Services\v1\Backend\PartnerCompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PartnerCompanyController extends Controller
{
    protected PartnerCompanyService $partnerCompanyService;

    public function __construct(PartnerCompanyService $partnerCompanyService)
    {
        $this->partnerCompanyService = $partnerCompanyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection|JsonResponse
    {
        $partnerCompanies = $this->partnerCompanyService->getPartnerCompanies($request->all());

        return PartnerCompanyResource::collection($partnerCompanies);
    }

    /**
     * Store newly created resources in storage (one per uploaded logo, max 12).
     */
    public function store(PartnerCompanyRequest $request): JsonResponse
    {
        $partnerCompanies = $this->partnerCompanyService->storePartnerCompanies($request->validated());

        return successResponse(PartnerCompanyResource::collection($partnerCompanies), 'Partner companies created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(PartnerCompany $partnerCompany): JsonResponse
    {
        $partnerCompany = $this->partnerCompanyService->getPartnerCompany($partnerCompany);

        return successResponse(new PartnerCompanyResource($partnerCompany), 'Partner company retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartnerCompanyRequest $request, PartnerCompany $partnerCompany): JsonResponse
    {
        $partnerCompany = $this->partnerCompanyService->updatePartnerCompany($request->validated(), $partnerCompany);

        return successResponse(new PartnerCompanyResource($partnerCompany), 'Partner company updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PartnerCompany $partnerCompany): JsonResponse
    {
        $this->partnerCompanyService->deletePartnerCompany($partnerCompany);

        return successResponse(new PartnerCompanyResource($partnerCompany), 'Partner company deleted successfully');
    }

    /**
     * List active partner companies for public consumption.
     */
    public function activePartnerCompanies(): ResourceCollection|JsonResponse
    {
        $partnerCompanies = $this->partnerCompanyService->activePartnerCompanies();

        return PartnerCompanyResource::collection($partnerCompanies);
    }
}
