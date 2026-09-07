<?php

namespace App\Models\Authorization;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    function subRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'assign_role', 'role_id', 'assign_role_id');
    }

    function mainRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'assign_role',  'assign_role_id', 'role_id');
    }

    function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

    function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    function scopeDataSearch($query, $data): void
    {
        $query->where(function ($q) use ($data) {
            if (!empty($data['search_data'])) {
                $q->where('name', 'like', '%' . $data['search_data'] . '%');
            }
            if (!empty($data['is_active'])) {
                $q->where('is_active', $data['is_active'] == 1 ? true : false);
            }
        });
    }
}
