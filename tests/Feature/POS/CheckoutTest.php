<?php

namespace Tests\Feature\POS;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_persists_sale_items_payments_and_stock_deduction(): void
    {
        [$branch, $user, $product] = $this->saleFixture(10);

        $response = $this->actingAs($user)->post(route('pos.checkout'), [
            'customer_name' => 'Walk-in',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
            'payments' => [
                ['method' => 'cash', 'amount' => 25, 'reference' => null],
            ],
        ]);

        $response->assertRedirect(route('pos.index'));

        $this->assertDatabaseCount('sales', 1);
        $this->assertDatabaseHas('sales', [
            'branch_id' => $branch->id,
            'user_id' => $user->id,
            'customer_name' => 'Walk-in',
            'subtotal' => 20,
            'grand_total' => 20,
            'paid_total' => 25,
            'change_total' => 5,
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('sale_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'line_total' => 20,
        ]);

        $this->assertDatabaseHas('sale_payments', [
            'method' => 'cash',
            'amount' => 25,
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 8,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'type' => 'sale',
            'quantity_delta' => -2,
            'quantity_after' => 8,
        ]);
    }

    public function test_checkout_rolls_back_when_stock_is_insufficient(): void
    {
        [$branch, $user, $product] = $this->saleFixture(1);

        $response = $this->actingAs($user)
            ->from(route('pos.index'))
            ->post(route('pos.checkout'), [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2],
                ],
                'payments' => [
                    ['method' => 'cash', 'amount' => 20, 'reference' => null],
                ],
            ]);

        $response->assertRedirect(route('pos.index'));
        $response->assertSessionHasErrors('items');

        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);
        $this->assertDatabaseCount('sale_payments', 0);
        $this->assertDatabaseCount('stock_movements', 0);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 1,
        ]);
    }

    /**
     * @return array{Branch, User, Product}
     */
    protected function saleFixture(int|float $stockQuantity): array
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
            'name' => 'Counter Item',
            'slug' => 'counter-item',
            'unit_name' => 'pcs',
            'price' => 10,
            'cost' => 4,
            'tax_rate' => 0,
            'track_stock' => true,
            'allow_negative_stock' => false,
            'is_active' => true,
        ]);

        ProductStock::query()->create([
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => $stockQuantity,
        ]);

        return [$branch, $user, $product];
    }
}
