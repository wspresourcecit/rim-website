<?php

namespace App\Models;

use App\Cache\Cacheable;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    use Cacheable, TracksUserActions;

    protected $table = 'news';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    public function scopeDataFilter(Builder $query, array $data): Builder
    {
        return $query->where(function ($q) use ($data) {
            if (! empty($data['search_data'])) {
                $q->where(function ($q2) use ($data) {
                    $q2->where('title', 'like', '%'.$data['search_data'].'%')
                        ->orWhere('excerpt', 'like', '%'.$data['search_data'].'%');
                });
            }
            if (! empty($data['category_id'])) {
                $q->where('category_id', $data['category_id']);
            }
            if (! empty($data['is_active'])) {
                $q->where('is_active', $data['is_active'] == 1 ? true : false);
            }
        });
    }
}
