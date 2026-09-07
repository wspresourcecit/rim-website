<?php

namespace App\Services\v1\Backend\Authorization;

use Exception;
use App\Models\Authorization\Role;
use Illuminate\Support\Facades\DB;
use App\Models\Authorization\PermissionGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleManageService
{
    public function getRoles(array $data): LengthAwarePaginator
    {
        return Role::with(['createdBy:id,name,created_at'])
            ->withCount('permissions')
            ->DataSearch($data)
            ->orderBy('id', 'DESC')
            ->paginate($data['paginate'] ?? config('app.paginate'));
    }

    public function storeRole(array $data, int $userId): Role
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['created_by'] =  $userId;
            $role = Role::create($data);
            $role->load('createdBy:id,name,created_at');
            $role->permissions()->sync($data['permissions']);
            $role->permissions_count = count(array_unique($data['permissions']));
            DB::commit();
            return $role;
        });
    }

    public function getRole($role): Role
    {
        $role->permission_id = $role->permissions()->pluck('permissions.id');
        return $role;
    }

    public function updateRole(array $data, $role, int $userId): Role
    {

        return DB::transaction(function () use ($data, $role, $userId) {
            $data['created_by'] = $userId;
            $role->update($data);
            if ($role->deletable) {
                $role->permissions()->sync($data['permissions']);
            } else {
                $role->permissions()->syncWithoutDetaching($data['permissions']);
            }
            $role->load('createdBy:id,name,created_at');
            $role->permissions_count = count(array_unique($data['permissions']));

            return $role;
        });
    }

    public function deleteRole($role): string
    {
        if (!$role->deletable) {
            throw new Exception("Could not delete!", 403);
        }

        $role->delete();
        return "Successfully deleted.";
    }

    public function getPermissions(): Collection
    {

        return PermissionGroup::with('permissions:id,permission_group_id,name')->select('id', 'name')->get();
    }

    function getActiveRoles(): Collection
    {

        $role = Role::where('is_active', true)->where('id', '!=', 1)->where('deletable', true)->select('id', 'name')->get();
        return $role;
    }
}
