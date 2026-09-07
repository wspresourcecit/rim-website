<?php

namespace App\Services\v1\Backend\News;

use App\Models\NewsCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class NewsCategoryService
{
    /**
     * Active categories for the website sidebar + admin dropdowns. Cached;
     * any NewsCategory write invalidates it.
     */
    public function getActiveCategories(): Collection
    {
        return NewsCategory::cacheRows(
            'active',
            [NewsCategory::class.':list'],
            fn () => NewsCategory::where('is_active', true)
                ->select('id', 'name', 'slug')
                ->orderBy('name', 'asc')
                ->get(),
        );
    }

    public function getCategories(array $data): LengthAwarePaginator
    {
        return NewsCategory::with(['createdBy:id,name,email,image', 'updatedBy:id,name,email,image'])
            ->withCount('news')
            ->DataFilter($data)
            ->orderBy('name', 'asc')
            ->select('id', 'name', 'slug', 'is_active', 'created_at', 'updated_by', 'created_by')
            ->paginate($data['paginate'] ?? config('app.paginate'));
    }

    public function storeCategory(array $data): NewsCategory
    {
        $data['slug'] = $this->uniqueSlug($data['name']);
        $category = NewsCategory::create($data);

        return $this->loadData($category);
    }

    public function getCategory(NewsCategory $category): NewsCategory
    {
        return $category;
    }

    public function updateCategory(array $data, NewsCategory $category): NewsCategory
    {
        $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        $category->update($data);

        return $this->loadData($category);
    }

    public function deleteCategory(NewsCategory $category): bool
    {
        return $category->delete();
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'category';
        $slug = $base;
        $i = 2;

        while (
            NewsCategory::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function loadData(NewsCategory $category): NewsCategory
    {
        return $category->load(['createdBy:id,name,email,image', 'updatedBy:id,name,email,image']);
    }
}
