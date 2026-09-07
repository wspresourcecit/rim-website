<?php

namespace App\Http\Controllers\v1\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\PageHeaderRequest;
use App\Http\Resources\v1\Backend\PageHeaderResource;
use App\Services\v1\Backend\PageHeaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageHeaderController extends Controller
{
    protected PageHeaderService $service;

    public function __construct(PageHeaderService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the page header for the given page key.
     */
    public function show(Request $request): JsonResponse
    {
        $pageHeader = $this->service->getPageHeader($request->all());
        return successResponse(new PageHeaderResource($pageHeader), 'Page header retrieved successfully');
    }

    /**
     * Update the page header for the given page key.
     */
    public function update(PageHeaderRequest $request): JsonResponse
    {
        $pageHeader = $this->service->updatePageHeader($request->validated());
        return successResponse(new PageHeaderResource($pageHeader), 'Page header updated successfully');
    }
}
