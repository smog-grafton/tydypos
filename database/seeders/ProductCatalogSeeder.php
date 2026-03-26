<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::query()->updateOrCreate(
            ['slug' => 'general'],
            ['name' => 'General', 'is_active' => true],
        );

        $products = [
            [
                'sku' => 'COF-001',
                'barcode' => '100000000001',
                'name' => 'House Coffee',
                'slug' => 'house-coffee',
                'unit_name' => 'cup',
                'price' => 3.50,
                'cost' => 1.10,
                'tax_rate' => 0,
            ],
            [
                'sku' => 'SNK-001',
                'barcode' => '100000000002',
                'name' => 'Salted Chips',
                'slug' => 'salted-chips',
                'unit_name' => 'pack',
                'price' => 1.80,
                'cost' => 0.70,
                'tax_rate' => 0,
            ],
        ];

        $branchId = \App\Models\Branch::query()->where('code', 'HQ')->value('id');

        foreach ($products as $payload) {
            $product = Product::query()->updateOrCreate(
                ['sku' => $payload['sku']],
                array_merge($payload, [
                    'category_id' => $category->id,
                    'track_stock' => true,
                    'allow_negative_stock' => false,
                    'is_active' => true,
                ]),
            );

            ProductStock::query()->updateOrCreate(
                ['product_id' => $product->id, 'branch_id' => $branchId],
                ['quantity' => 25],
            );
        }
    }
}
