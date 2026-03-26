<?php

namespace App\Modules\Inventory\Actions;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdjustStockAction
{
    /**
     * @param  array{branch_id:int, quantity_delta:numeric-string|int|float, type:string, notes:?string}  $payload
     */
    public function execute(Product $product, User $user, array $payload): ProductStock
    {
        return DB::transaction(function () use ($product, $user, $payload): ProductStock {
            $stock = ProductStock::query()
                ->where('product_id', $product->id)
                ->where('branch_id', $payload['branch_id'])
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                $stock = ProductStock::query()->create([
                    'product_id' => $product->id,
                    'branch_id' => $payload['branch_id'],
                    'quantity' => 0,
                ]);

                $stock->refresh();
            }

            $currentQuantity = (float) $stock->quantity;
            $delta = round((float) $payload['quantity_delta'], 3);
            $newQuantity = round($currentQuantity + $delta, 3);

            if ($newQuantity < 0 && ! $product->allow_negative_stock) {
                throw ValidationException::withMessages([
                    'quantity_delta' => __('Insufficient stock for this adjustment.'),
                ]);
            }

            $stock->update([
                'quantity' => $newQuantity,
            ]);

            StockMovement::query()->create([
                'product_id' => $product->id,
                'branch_id' => $payload['branch_id'],
                'user_id' => $user->id,
                'type' => $payload['type'],
                'quantity_delta' => $delta,
                'quantity_after' => $newQuantity,
                'notes' => $payload['notes'],
            ]);

            Log::info('inventory.adjusted', [
                'product_id' => $product->id,
                'branch_id' => $payload['branch_id'],
                'user_id' => $user->id,
                'delta' => $delta,
                'quantity_after' => $newQuantity,
                'type' => $payload['type'],
            ]);

            return $stock->fresh();
        });
    }
}
