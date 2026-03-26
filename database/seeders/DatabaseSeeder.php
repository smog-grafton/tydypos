<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
            RoleAndPermissionSeeder::class,
            SettingsSeeder::class,
            ProductCatalogSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
