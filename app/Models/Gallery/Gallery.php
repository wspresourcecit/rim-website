<?php

namespace App\Models\Gallery;

use App\Cache\Cacheable;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    use Cacheable, TracksUserActions;

    protected $table = 'galleries';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The frontend gallery page + getTypeGallery() list carry each type's
     * active images, so a Gallery write must invalidate GalleryType's lists.
     */
    protected static function booted(): void
    {
        static::invalidatesModelsBooted([GalleryType::class]);
    }

    /**
     * Get the gallery type for this image.
     */
    public function galleryType(): BelongsTo
    {
        return $this->belongsTo(GalleryType::class, 'gallery_type_id');
    }

    public function scopeDataFilter(Builder $query, array $data): Builder
    {
        return $query->where(function ($q) use ($data) {

            if (! empty($data['search_data'])) {
                $q->where('title', 'like', '%'.$data['search_data'].'%');
            }

            if (! empty($data['is_active'])) {
                $q->where('is_active', $data['is_active'] == 1 ? true : false);
            }

            if (! empty($data['gallery_type_id'])) {
                $q->where('gallery_type_id', $data['gallery_type_id']);
            }
        });
    }
}
