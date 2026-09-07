<?php

namespace App\Models\Gallery;

use App\Cache\Cacheable;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryType extends Model
{
    use Cacheable, TracksUserActions;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * getTypeGallery() keys its list by type name, so a GalleryType write
     * must invalidate Gallery's lists too.
     */
    protected static function booted(): void
    {
        static::invalidatesModelsBooted([Gallery::class]);
    }

    /**
     * Get all gallery images for this type.
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'gallery_type_id');
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
