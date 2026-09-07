<?php

namespace App\Services\v1\Backend\Faq;

use App\Models\Faq\Faq;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FaqService
{
    /**
     * Featured active FAQs for the frontend home "FAQ" section. Cached; a Faq
     * (or FaqCategory) write invalidates it via the Cacheable trait.
     */
    public function homeFeaturedFaqs(): Collection
    {
        return Faq::cacheRows(
            'home:featured',
            [Faq::class.':list'],
            fn () => Faq::where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('id')
                ->get(['id', 'question', 'answer']),
        );
    }

    /**
     * Active FAQs under an active category, for the FAQ page accordion. Cached.
     */
    public function activePageFaqs(): Collection
    {
        return Faq::cacheRows(
            'page:active',
            [Faq::class.':list'],
            fn () => Faq::where('is_active', true)
                ->whereHas('category', fn ($q) => $q->where('is_active', true))
                ->orderBy('id')
                ->get(['id', 'category_id', 'question', 'answer']),
        );
    }

    public function getFaqs(array $data): LengthAwarePaginator
    {
        return Faq::with(['category:id,name', 'createdBy:id,name,email,image', 'updatedBy:id,name,email,image'])
            ->DataFilter($data)
            ->select('id', 'category_id', 'question', 'answer', 'is_active', 'is_featured', 'created_at', 'updated_by', 'created_by')
            ->orderBy('id', 'desc')
            ->paginate($data['paginate'] ?? config('app.paginate'));
    }

    public function storeFaq(array $data): Faq
    {
        $faq = Faq::create($data);

        return $this->loadData($faq);
    }

    public function getFaq(Faq $faq): Faq
    {
        return $this->loadData($faq);
    }

    public function updateFaq(array $data, Faq $faq): Faq
    {
        $faq->update($data);

        return $this->loadData($faq);
    }

    public function deleteFaq(Faq $faq): bool
    {
        return $faq->delete();
    }

    private function loadData(Faq $faq): Faq
    {
        return $faq->load(['category:id,name', 'createdBy:id,name,email,image', 'updatedBy:id,name,email,image']);
    }
}
