<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\App\Enums\Permission;
use Modules\Auth\App\Enums\Role;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (Permission::cases() as $permission) {
            SpatiePermission::firstOrCreate(
                ['name' => $permission->value],
                ['guard_name' => 'web']
            );
        }

        foreach (Role::cases() as $role) {
            $spatieRole = SpatieRole::firstOrCreate(
                ['name' => $role->value],
                ['guard_name' => 'web']
            );

            $permissionValues = array_map(
                fn (Permission $p) => $p->value,
                Permission::matrixForRole($role)
            );

            $spatieRole->syncPermissions($permissionValues);
        }
    }
}
