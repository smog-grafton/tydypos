<?php

namespace Tests\Unit\Auth;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_inherits_permissions_from_assigned_role(): void
    {
        $permission = Permission::query()->create([
            'name' => 'pos.sell',
            'label' => 'Process sales',
            'module' => 'pos',
        ]);

        $role = Role::query()->create([
            'name' => 'cashier',
            'label' => 'Cashier',
        ]);

        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->assertTrue($user->hasPermission('pos.sell'));
        $this->assertFalse($user->hasPermission('settings.manage'));
    }
}
