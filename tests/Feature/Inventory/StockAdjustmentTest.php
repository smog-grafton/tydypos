<?php

namespace Tests\Feature\Inventory;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_adjustment_creates_stock_movement_and_updates_stock(): void
    {
        $branch = Branch::query()->create([
            'name' => 'Head Office',
            'code' => 'HQ',
            'currency_code' => 'USD',
            'timezone' => 'UTC',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'current_branch_id' => $branch->id,
        ]);

        $category = Category::query()->create([
            'name' => 'General',
            'slug' => 'general',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'sku' => 'SKU-1',
            'name' => 'Coffee',
            'slug' => 'coffee',
            'unit_name' => 'cup',
            'price' => 3.50,
            'cost' => 1.20,
            'tax_rate' => 0,
            'track_stock' => true,
            'allow_negative_stock' => false,
            'is_active' => true,
        ]);

        ProductStock::query()->create([
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('inventory.adjustments.store'), [
            'product_id' => $product->id,
            'quantity_delta' => 5,
            'type' => 'opening_stock',
            'notes' => 'Initial count',
        ]);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'type' => 'opening_stock',
            'quantity_delta' => 5,
            'quantity_after' => 5,
        ]);
    }
}
