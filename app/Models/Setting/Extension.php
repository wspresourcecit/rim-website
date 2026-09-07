<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Extension extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'credentials' => 'array',
        'is_api' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function ($extension) {
            Cache::forget("extension_credentials_{$extension->short_code}");
            Cache::forget("active_extensions_shared");

            if ($extension->wasChanged('short_code')) {
                $originalShortCode = $extension->getOriginal('short_code');
                Cache::forget("extension_credentials_{$originalShortCode}");
            }
        });

        static::deleted(function ($extension) {
            Cache::forget("extension_credentials_{$extension->short_code}");
            Cache::forget("active_extensions_shared");
        });
    }

    function scopeDataFilter(Builder $query, array $data): Builder
    {
        return $query->where(function ($q) use ($data) {

            if (!empty($data['search_data'])) {
                $q->where('name', 'like', '%' . $data['search_data'] . '%');
            }

            if (!empty($data['is_active'])) {
                $q->where('is_active', $data['is_active'] == 1 ? true : false);
            }
            if (!empty($data['is_api'])) {
                $q->where('is_api', $data['is_api'] == 1 ? true : false);
            }
        });
    }
}
