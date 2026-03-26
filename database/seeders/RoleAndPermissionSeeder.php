<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'dashboard.view', 'label' => 'View dashboard', 'module' => 'core'],
            ['name' => 'settings.manage', 'label' => 'Manage settings', 'module' => 'settings'],
            ['name' => 'users.manage', 'label' => 'Manage users', 'module' => 'auth'],
            ['name' => 'branches.manage', 'label' => 'Manage branches', 'module' => 'branches'],
            ['name' => 'products.manage', 'label' => 'Manage products', 'module' => 'products'],
            ['name' => 'inventory.adjust', 'label' => 'Adjust inventory', 'module' => 'inventory'],
            ['name' => 'pos.sell', 'label' => 'Process sales', 'module' => 'pos'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['name' => $permission['name']],
                $permission,
            );
        }

        $roles = [
            'owner' => ['dashboard.view', 'settings.manage', 'users.manage', 'branches.manage', 'products.manage', 'inventory.adjust', 'pos.sell'],
            'manager' => ['dashboard.view', 'branches.manage', 'products.manage', 'inventory.adjust', 'pos.sell'],
            'cashier' => ['dashboard.view', 'pos.sell'],
        ];

        foreach ($roles as $name => $mappedPermissions) {
            $role = Role::query()->updateOrCreate(
                ['name' => $name],
                ['label' => ucfirst($name)],
            );

            $role->permissions()->sync(
                Permission::query()->whereIn('name', $mappedPermissions)->pluck('id'),
            );
        }
    }
}
