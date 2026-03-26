<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::query()->updateOrCreate(
            ['code' => 'HQ'],
            [
                'name' => 'Head Office',
                'phone' => '+1 555 0100',
                'email' => 'hq@tydypos.test',
                'address' => '100 Market Street',
                'currency_code' => 'USD',
                'timezone' => 'UTC',
                'is_active' => true,
            ],
        );
    }
}
