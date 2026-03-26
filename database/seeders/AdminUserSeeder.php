<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::query()->where('code', 'HQ')->firstOrFail();
        $ownerRole = Role::query()->where('name', 'owner')->firstOrFail();

        $user = User::query()->updateOrCreate(
            ['email' => 'admin@tydypos.test'],
            [
                'name' => 'Tydy Owner',
                'password' => Hash::make('password'),
                'locale' => 'en',
                'is_active' => true,
                'current_branch_id' => $branch->id,
            ],
        );

        $user->roles()->sync([$ownerRole->id]);
    }
}
