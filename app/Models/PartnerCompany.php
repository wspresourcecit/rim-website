<?php

namespace App\Models;

use App\Cache\Cacheable;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PartnerCompany extends Model
{
    use Cacheable, TracksUserActions;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const TYPES = ['sister_concern', 'associated', 'member', 'hiring_partner', 'freelancing'];

    public function scopeDataFilter(Builder $query, array $data): Builder
    {
        return $query->where(function ($q) use ($data) {
            if (! empty($data['type'])) {
                $q->where('type', $data['type']);
            }
            if (! empty($data['is_active'])) {
                $q->where('is_active', $data['is_active'] == 1 ? true : false);
            }
        });
    }
}
