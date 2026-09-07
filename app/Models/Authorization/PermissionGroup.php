<?php

namespace App\Models\Authorization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_group_id');
    }
}
