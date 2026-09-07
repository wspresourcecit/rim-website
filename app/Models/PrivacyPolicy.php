<?php

namespace App\Models;

use App\Cache\Cacheable;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    use Cacheable, TracksUserActions;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeDataFilter(Builder $query, array $data): Builder
    {
        return $query->where(function ($q) use ($data) {

            if (! empty($data['search_data'])) {
                $q->where(function ($q2) use ($data) {
                    $q2->where('title', 'like', '%'.$data['search_data'].'%')
                        ->orWhere('description', 'like', '%'.$data['search_data'].'%');
                });
            }
            if (isset($data['is_active']) && $data['is_active'] !== '') {
                $q->where('is_active', $data['is_active'] == 1);
            }
        });
    }
}
