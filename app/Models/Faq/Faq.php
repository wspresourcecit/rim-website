<?php

namespace App\Models\Faq;

use App\Cache\Cacheable;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use Cacheable, TracksUserActions;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * A FAQ being added / toggled changes which categories have visible
     * FAQs, so a Faq write must invalidate the cached category list too.
     */
    protected static function booted(): void
    {
        static::invalidatesModelsBooted([FaqCategory::class]);
    }

    /**
     * Get the category for this faq.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }

    public function scopeDataFilter(Builder $query, array $data): Builder
    {
        return $query->where(function ($q) use ($data) {

            if (! empty($data['search_data'])) {
                $q->where(function ($q2) use ($data) {
                    $q2->where('question', 'like', '%'.$data['search_data'].'%')
                        ->orWhere('answer', 'like', '%'.$data['search_data'].'%');
                });
            }

            if (! empty($data['is_active'])) {
                $q->where('is_active', $data['is_active'] == 1 ? true : false);
            }

            if (! empty($data['is_featured'])) {
                $q->where('is_featured', $data['is_featured'] == 1 ? true : false);
            }

            if (! empty($data['category_id'])) {
                $q->where('category_id', $data['category_id']);
            }
        });
    }
}
