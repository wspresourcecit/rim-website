<?php

namespace App\Http\Controllers\v1\Backend\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\Gallery\GalleryRequest;
use App\Http\Resources\v1\Backend\Gallery\GalleryResource;
use App\Models\Gallery\Gallery;
use App\Services\v1\Backend\Gallery\GalleryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GalleryController extends Controller
{
    protected GalleryService $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection|JsonResponse
    {
        $galleries = $this->galleryService->getGalleries($request->all());
        return GalleryResource::collection($galleries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GalleryRequest $request): JsonResponse
    {
        $gallery = $this->galleryService->storeGallery($request->validated());
        return successResponse(new GalleryResource($gallery), 'Gallery image created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gallery $gallery): JsonResponse
    {
        $gallery = $this->galleryService->getGallery($gallery);
        return successResponse(new GalleryResource($gallery), 'Gallery image retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GalleryRequest $request, Gallery $gallery): JsonResponse
    {
        $gallery = $this->galleryService->updateGallery($request->validated(), $gallery);
        return successResponse(new GalleryResource($gallery), 'Gallery image updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery): JsonResponse
    {
        $this->galleryService->deleteGallery($gallery);
        return successResponse(new GalleryResource($gallery), 'Gallery image deleted successfully');
    }
}
