<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Authorization\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role_id', 'created_by', 'updated_by', 'image', 'is_active', 'deleteable'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    function check($permission): bool
    {
        return $this->role->permissions()->where('slug', $permission)->exists();
    }
    function scopeDataFilter($query, $data): void
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
