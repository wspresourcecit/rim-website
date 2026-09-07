<?php

namespace App\Http\Controllers\v1\Backend\Faq;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\Faq\FaqCategoryRequest;
use App\Http\Resources\v1\Backend\Faq\FaqCategoryResource;
use App\Models\Faq\FaqCategory;
use App\Services\v1\Backend\Faq\FaqCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FaqCategoryController extends Controller
{
    protected FaqCategoryService $categoryService;

    public function __construct(FaqCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection | JsonResponse
    {
        $categories = $this->categoryService->getCategories($request->all());
        return FaqCategoryResource::collection($categories);
    }

    /**
     * Display a listing of active categories.
     */
    public function activeCategories(): ResourceCollection
    {
        $categories = $this->categoryService->getActiveCategories();
        return FaqCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FaqCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->storeCategory($request->validated());
        return successResponse(new FaqCategoryResource($category), 'FAQ category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(FaqCategory $faqCategory): JsonResponse
    {
        $category = $this->categoryService->getCategory($faqCategory);
        return successResponse(new FaqCategoryResource($category), 'FAQ category retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FaqCategoryRequest $request, FaqCategory $faqCategory): JsonResponse
    {
        $category = $this->categoryService->updateCategory($request->validated(), $faqCategory);
        return successResponse(new FaqCategoryResource($category), 'FAQ category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FaqCategory $faqCategory): JsonResponse
    {
        $this->categoryService->deleteCategory($faqCategory);
        return successResponse(new FaqCategoryResource($faqCategory), 'FAQ category deleted successfully');
    }
}
