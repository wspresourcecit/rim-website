<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Authorization\Role;
use App\Models\Authorization\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate([
            'name' => "System Admin",
        ], [
            'created_by' => 1,
            'deletable' => false,
        ]);
        $permissions = Permission::pluck('id');
        $role->permissions()->sync($permissions);
    }
}
