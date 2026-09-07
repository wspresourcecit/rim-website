<?php

namespace App\Http\Controllers\v1\Backend\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\Gallery\GalleryTypeRequest;
use App\Http\Resources\v1\Backend\Gallery\GalleryTypeResource;
use App\Models\Gallery\GalleryType;
use App\Services\v1\Backend\Gallery\GalleryTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GalleryTypeController extends Controller
{
    protected GalleryTypeService $typeService;

    public function __construct(GalleryTypeService $typeService)
    {
        $this->typeService = $typeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection|JsonResponse
    {
        $types = $this->typeService->getTypes($request->all());
        return GalleryTypeResource::collection($types);
    }

    /**
     * Display a listing of active gallery types.
     */
    public function activeTypes(): ResourceCollection
    {
        $types = $this->typeService->getActiveTypes();
        return GalleryTypeResource::collection($types);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GalleryTypeRequest $request): JsonResponse
    {
        $type = $this->typeService->storeType($request->validated());
        return successResponse(new GalleryTypeResource($type), 'Gallery type created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(GalleryType $galleryType): JsonResponse
    {
        $type = $this->typeService->getType($galleryType);
        return successResponse(new GalleryTypeResource($type), 'Gallery type retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GalleryTypeRequest $request, GalleryType $galleryType): JsonResponse
    {
        $type = $this->typeService->updateType($request->validated(), $galleryType);
        return successResponse(new GalleryTypeResource($type), 'Gallery type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GalleryType $galleryType): JsonResponse
    {
        $this->typeService->deleteType($galleryType);
        return successResponse(new GalleryTypeResource($galleryType), 'Gallery type deleted successfully');
    }
}
