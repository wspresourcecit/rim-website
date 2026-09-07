<?php

namespace App\Http\Controllers\v1\Backend\Faq;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\Faq\FaqRequest;
use App\Http\Resources\v1\Backend\Faq\FaqResource;
use App\Models\Faq\Faq;
use App\Services\v1\Backend\Faq\FaqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FaqController extends Controller
{
    protected FaqService $faqService;

    public function __construct(FaqService $faqService)
    {
        $this->faqService = $faqService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection | JsonResponse
    {
        $faqs = $this->faqService->getFaqs($request->all());
        return FaqResource::collection($faqs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FaqRequest $request): JsonResponse
    {
        $faq = $this->faqService->storeFaq($request->validated());
        return successResponse(new FaqResource($faq), 'FAQ created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq): JsonResponse
    {
        $faq = $this->faqService->getFaq($faq);
        return successResponse(new FaqResource($faq), 'FAQ retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FaqRequest $request, Faq $faq): JsonResponse
    {
        $faq = $this->faqService->updateFaq($request->validated(), $faq);
        return successResponse(new FaqResource($faq), 'FAQ updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq): JsonResponse
    {
        $this->faqService->deleteFaq($faq);
        return successResponse(new FaqResource($faq), 'FAQ deleted successfully');
    }
}
