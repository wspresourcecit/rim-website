<?php

namespace App\Models\Setting;

use App\Cache\Cacheable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebsiteAsset extends Model
{
    use Cacheable;

    protected $guarded = ['id'];

    protected $casts = [
        'maintenance_mode' => 'boolean',
        'is_active' => 'boolean',
        'is_auto_backup' => 'boolean',
        'social_handles' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('website_asset_shared');
        });

        static::deleted(function () {
            Cache::forget('website_asset_shared');
        });
    }
}
