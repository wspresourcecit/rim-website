<?php

namespace App\Http\Controllers\v1\Backend\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\Setting\ExtensionRequest;
use App\Http\Resources\v1\Backend\Setting\ExtensionResource;
use App\Models\Setting\Extension;
use App\Services\v1\Backend\Setting\ExtensionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    protected $service;

    public function __construct(ExtensionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): ResourceCollection | JsonResponse
    {
        try {
            $Extensions = $this->service->getExtensions($request->all());
            return ExtensionResource::collection($Extensions);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function show(Extension $Extension): JsonResponse
    {
        try {
            return successResponse(new ExtensionResource($Extension));
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        }

    }

    public function update(ExtensionRequest $request, Extension $Extension): JsonResponse
    {
        try {
            $Extension = $this->service->updateExtension($request->validated(),$Extension);
            return successResponse(new ExtensionResource($Extension),'Extension has been updated successfully.');
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
