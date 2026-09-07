<?php

namespace App\Http\Controllers\v1\Backend\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\News\NewsCategoryRequest;
use App\Http\Resources\v1\Backend\News\NewsCategoryResource;
use App\Models\NewsCategory;
use App\Services\v1\Backend\News\NewsCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NewsCategoryController extends Controller
{
    protected NewsCategoryService $categoryService;

    public function __construct(NewsCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection | JsonResponse
    {
        $categories = $this->categoryService->getCategories($request->all());

        return NewsCategoryResource::collection($categories);
    }

    /**
     * Display a listing of active categories.
     */
    public function activeCategories(): ResourceCollection
    {
        $categories = $this->categoryService->getActiveCategories();

        return NewsCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewsCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->storeCategory($request->validated());

        return successResponse(new NewsCategoryResource($category), 'News category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(NewsCategory $newsCategory): JsonResponse
    {
        $this->categoryService->getCategory($newsCategory);

        return successResponse(new NewsCategoryResource($newsCategory), 'News category retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NewsCategoryRequest $request, NewsCategory $newsCategory): JsonResponse
    {
        $this->categoryService->updateCategory($request->validated(), $newsCategory);

        return successResponse(new NewsCategoryResource($newsCategory), 'News category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsCategory $newsCategory): JsonResponse
    {
        $this->categoryService->deleteCategory($newsCategory);

        return successResponse(new NewsCategoryResource($newsCategory), 'News category deleted successfully');
    }
}
