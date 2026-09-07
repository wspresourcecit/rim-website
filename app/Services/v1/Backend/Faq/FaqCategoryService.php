<?php

namespace App\Services\v1\Backend\Faq;

use App\Models\Faq\FaqCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FaqCategoryService
{
    public function getActiveCategories(): Collection
    {
        return FaqCategory::where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Active categories that have at least one active FAQ, for the FAQ page
     * filter tabs. Cached; a FaqCategory or Faq write invalidates it.
     */
    public function activeCategoriesWithFaqs(): Collection
    {
        return FaqCategory::cacheRows(
            'page:active-with-faqs',
            [FaqCategory::class.':list'],
            fn () => FaqCategory::where('is_active', true)
                ->whereHas('faqs', fn ($q) => $q->where('is_active', true))
                ->orderBy('id')
                ->get(['id', 'name']),
        );
    }

    public function getCategories(array $data): LengthAwarePaginator
    {
        return FaqCategory::with(['createdBy:id,name,email,image', 'updatedBy:id,name,email,image'])
            ->DataFilter($data)
            ->select('id', 'name', 'is_active', 'created_at', 'updated_by', 'created_by')
            ->orderBy('id', 'desc')
            ->paginate($data['paginate'] ?? config('app.paginate'));
    }

    public function storeCategory(array $data): FaqCategory
    {
        $category = FaqCategory::create($data);

        return $this->loadData($category);
    }

    public function getCategory(FaqCategory $faqCategory): FaqCategory
    {
        return $this->loadData($faqCategory);
    }

    public function updateCategory(array $data, FaqCategory $faqCategory): FaqCategory
    {
        $faqCategory->update($data);

        return $this->loadData($faqCategory);
    }

    public function deleteCategory(FaqCategory $faqCategory): bool
    {
        return $faqCategory->delete();
    }

    private function loadData(FaqCategory $faqCategory): FaqCategory
    {
        return $faqCategory->load(['createdBy:id,name,email,image', 'updatedBy:id,name,email,image']);
    }
}
