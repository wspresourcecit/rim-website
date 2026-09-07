<?php

namespace App\Models\Faq;

use App\Cache\Cacheable;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqCategory extends Model
{
    use Cacheable, TracksUserActions;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * A category becoming active / inactive changes which FAQs the frontend
     * shows, so a FaqCategory write must invalidate the cached FAQ lists too.
     */
    protected static function booted(): void
    {
        static::invalidatesModelsBooted([Faq::class]);
    }

    /**
     * Get all faqs for this category.
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'category_id');
    }

    public function scopeDataFilter(Builder $query, array $data): Builder
    {
        return $query->where(function ($q) use ($data) {

            if (! empty($data['search_data'])) {
                $q->where('name', 'like', '%'.$data['search_data'].'%');
            }

            if (! empty($data['is_active'])) {
                $q->where('is_active', $data['is_active'] == 1 ? true : false);
            }
        });
    }
}
