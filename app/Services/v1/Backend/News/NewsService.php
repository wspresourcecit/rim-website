<?php

namespace App\Services\v1\Backend\News;

use App\Models\News;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class NewsService
{
    private const RELATIONS = [
        'category:id,name,slug',
        'createdBy:id,name,email,image',
        'updatedBy:id,name,email,image',
    ];

    /* --------------------------------------------------------------------
     |  Admin
     | ------------------------------------------------------------------ */

    public function getNewsList(array $data): LengthAwarePaginator
    {
        return News::with(self::RELATIONS)
            ->DataFilter($data)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($data['paginate'] ?? config('app.paginate'));
    }

    public function storeNews(array $data): News
    {
        $data['image'] = uploadBase64Image($data['image'], 'images/cms/news', 'news');
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title']);
        $data['published_at'] = ! empty($data['published_at']) ? $data['published_at'] : now();

        $news = News::create($data);

        return $this->loadData($news);
    }

    public function getNews(News $news): News
    {
        return $this->loadData($news);
    }

    public function updateNews(array $data, News $news): News
    {
        if (! empty($data['image'])) {
            $newImage = uploadBase64Image($data['image'], 'images/cms/news', 'news');
            deleteImage($news->image);
            $data['image'] = $newImage;
        } else {
            unset($data['image']);
        }

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title'], $news->id);

        if (array_key_exists('published_at', $data) && empty($data['published_at'])) {
            $data['published_at'] = $news->published_at ?? now();
        }

        $news->update($data);

        return $this->loadData($news);
    }

    public function deleteNews(News $news): bool
    {
        deleteImage($news->image);

        return $news->delete();
    }

    /* --------------------------------------------------------------------
     |  Website (cached)
     | ------------------------------------------------------------------ */

    /**
     * A page of published posts for /news + the "load more" AJAX, optionally
     * filtered by category id and/or a search term. Count and relation-loaded
     * rows are cached separately per scope, then reassembled into a paginator.
     * Any News / NewsCategory write invalidates every scope.
     */
    public function paginateActivePosts(int $perPage, int $page = 1, ?int $categoryId = null, ?string $search = null): LengthAwarePaginator
    {
        $page = max(1, $page);
        $search = $search !== null ? trim($search) : null;

        $filters = array_filter([
            'category_id' => $categoryId,
            'search' => $search,
        ], fn ($v) => $v !== null && $v !== '');
        ksort($filters);
        $scope = $filters === [] ? 'all' : substr(md5(json_encode($filters)), 0, 12);

        $base = fn () => News::query()
            ->where('is_active', true)
            ->where('published_at', '<=', now())
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->where('title', 'like', '%'.$search.'%')
                    ->orWhere('excerpt', 'like', '%'.$search.'%');
            }));

        $total = (int) News::cacheRememberWithTags(
            "list:{$scope}:count",
            [News::class.':list'],
            fn () => $base()->count(),
        );

        $rows = News::cacheTree(
            "list:{$scope}:pp{$perPage}:p{$page}",
            [News::class.':list'],
            ['category'],
            fn () => $base()
                ->with('category:id,name,slug')
                ->select('id', 'title', 'slug', 'excerpt', 'image', 'category_id', 'published_at')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->forPage($page, $perPage)
                ->get(),
        );

        return new LengthAwarePaginator($rows, $total, $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /**
     * A single published post with its category, for the detail page. The
     * route already resolves the record; this caches its relation tree.
     */
    public function detailWithRelations(News $news): News
    {
        return News::cacheTreeOne(
            "detail:{$news->id}",
            [News::class.':list'],
            ['category'],
            fn () => $news->loadMissing(['category:id,name,slug']),
        );
    }

    /**
     * Newest published posts from the same category (excluding the current
     * one) for the "Related Posts" strip. Cached per post.
     */
    public function relatedPosts(News $news, int $limit = 3): EloquentCollection
    {
        return News::cacheTree(
            "related:{$news->id}",
            [News::class.':list'],
            ['category'],
            fn () => News::query()
                ->where('is_active', true)
                ->where('published_at', '<=', now())
                ->where('category_id', $news->category_id)
                ->where('id', '!=', $news->id)
                ->with('category:id,name,slug')
                ->select('id', 'title', 'slug', 'excerpt', 'image', 'category_id', 'published_at')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit($limit)
                ->get(),
        );
    }

    /* --------------------------------------------------------------------
     |  Helpers
     | ------------------------------------------------------------------ */

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        // Str::slug() drops non-ASCII, so Bangla-only titles collapse to '' — fall back.
        $base = Str::slug($value) ?: 'news';
        $slug = $base;
        $i = 2;

        while (
            News::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function loadData(News $news): News
    {
        return $news->load(self::RELATIONS);
    }
}
