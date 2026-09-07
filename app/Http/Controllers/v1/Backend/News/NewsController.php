<?php

namespace App\Http\Controllers\v1\Backend\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\News\NewsRequest;
use App\Http\Resources\v1\Backend\News\NewsResource;
use App\Models\News;
use App\Services\v1\Backend\News\NewsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NewsController extends Controller
{
    protected NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection | JsonResponse
    {
        $news = $this->newsService->getNewsList($request->all());

        return NewsResource::collection($news);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewsRequest $request): JsonResponse
    {
        $news = $this->newsService->storeNews($request->validated());

        return successResponse(new NewsResource($news), 'News post created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news): JsonResponse
    {
        $news = $this->newsService->getNews($news);

        return successResponse(new NewsResource($news), 'News post retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NewsRequest $request, News $news): JsonResponse
    {
        $news = $this->newsService->updateNews($request->validated(), $news);

        return successResponse(new NewsResource($news), 'News post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news): JsonResponse
    {
        $this->newsService->deleteNews($news);

        return successResponse(new NewsResource($news), 'News post deleted successfully');
    }
}
