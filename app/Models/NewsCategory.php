<?php

namespace App\Models;

use App\Cache\Cacheable;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsCategory extends Model
{
    use Cacheable, TracksUserActions;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** News cards embed the category name — flush News on write. */
    protected static function booted(): void
    {
        static::invalidatesModelsBooted([News::class]);
    }

    /**
     * Get all news posts for this category.
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'category_id');
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
