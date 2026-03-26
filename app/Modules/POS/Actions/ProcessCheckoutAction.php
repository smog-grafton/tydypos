<?php

namespace App\Modules\POS\Actions;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockMovement;
use App\Modules\POS\Data\CheckoutData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProcessCheckoutAction
{
    public function execute(CheckoutData $checkoutData): Sale
    {
        return DB::transaction(function () use ($checkoutData): Sale {
            $preparedItems = [];
            $subtotal = 0.0;
            $taxTotal = 0.0;
            $paidTotal = round(collect($checkoutData->payments)->sum(fn (array $payment) => (float) $payment['amount']), 2);

            foreach ($checkoutData->items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);

                if (! $product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => __("The product ':name' is inactive.", ['name' => $product->name]),
                    ]);
                }

                $quantity = round((float) $item['quantity'], 3);

                if ($quantity <= 0) {
                    throw ValidationException::withMessages([
                        'items' => __('Each checkout item must have a quantity greater than zero.'),
                    ]);
                }

                $stock = ProductStock::query()
                    ->where('product_id', $product->id)
                    ->where('branch_id', $checkoutData->branchId)
                    ->lockForUpdate()
                    ->first();

                if (! $stock) {
                    $stock = ProductStock::query()->create([
                        'product_id' => $product->id,
                        'branch_id' => $checkoutData->branchId,
                        'quantity' => 0,
                    ]);
                }

                $available = round((float) $stock->quantity, 3);
                $nextQuantity = round($available - $quantity, 3);

                if ($product->track_stock && $nextQuantity < 0 && ! $product->allow_negative_stock) {
                    throw ValidationException::withMessages([
                        'items' => __("Insufficient stock for ':name'.", ['name' => $product->name]),
                    ]);
                }

                $lineSubtotal = round($quantity * (float) $product->price, 2);
                $lineTax = round($lineSubtotal * (((float) $product->tax_rate) / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;

                $preparedItems[] = [
                    'product' => $product,
                    'stock' => $stock,
                    'quantity' => $quantity,
                    'next_quantity' => $nextQuantity,
                    'line_subtotal' => $lineSubtotal,
                    'line_tax' => $lineTax,
                    'line_total' => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 2);
            $taxTotal = round($taxTotal, 2);
            $grandTotal = round($subtotal + $taxTotal, 2);
            $changeTotal = round(max(0, $paidTotal - $grandTotal), 2);
            $paymentStatus = $paidTotal >= $grandTotal
                ? 'paid'
                : ($paidTotal > 0 ? 'partial' : 'unpaid');

            $sale = Sale::query()->create([
                'branch_id' => $checkoutData->branchId,
                'user_id' => $checkoutData->userId,
                'sale_number' => $this->generateSaleNumber(),
                'customer_name' => $checkoutData->customerName,
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'discount_total' => 0,
                'grand_total' => $grandTotal,
                'paid_total' => $paidTotal,
                'change_total' => $changeTotal,
                'payment_status' => $paymentStatus,
                'status' => 'completed',
                'notes' => $checkoutData->notes,
            ]);

            foreach ($preparedItems as $preparedItem) {
                $product = $preparedItem['product'];
                $stock = $preparedItem['stock'];

                SaleItem::query()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'unit_name' => $product->unit_name,
                    'quantity' => $preparedItem['quantity'],
                    'unit_price' => $product->price,
                    'unit_cost' => $product->cost,
                    'tax_rate' => $product->tax_rate,
                    'tax_total' => $preparedItem['line_tax'],
                    'discount_total' => 0,
                    'line_subtotal' => $preparedItem['line_subtotal'],
                    'line_total' => $preparedItem['line_total'],
                ]);

                if ($product->track_stock) {
                    $stock->update([
                        'quantity' => $preparedItem['next_quantity'],
                    ]);

                    StockMovement::query()->create([
                        'product_id' => $product->id,
                        'branch_id' => $checkoutData->branchId,
                        'user_id' => $checkoutData->userId,
                        'type' => 'sale',
                        'quantity_delta' => -1 * $preparedItem['quantity'],
                        'quantity_after' => $preparedItem['next_quantity'],
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'notes' => 'Checkout sale deduction',
                    ]);
                }
            }

            foreach ($checkoutData->payments as $payment) {
                SalePayment::query()->create([
                    'sale_id' => $sale->id,
                    'method' => $payment['method'],
                    'amount' => round((float) $payment['amount'], 2),
                    'reference' => $payment['reference'] ?? null,
                    'paid_at' => now(),
                ]);
            }

            Log::info('sales.checkout.completed', [
                'sale_id' => $sale->id,
                'sale_number' => $sale->sale_number,
                'branch_id' => $checkoutData->branchId,
                'user_id' => $checkoutData->userId,
                'grand_total' => $grandTotal,
                'paid_total' => $paidTotal,
                'items_count' => count($preparedItems),
            ]);

            return $sale->load(['items', 'payments']);
        });
    }

    protected function generateSaleNumber(): string
    {
        return 'SAL-'.now()->format('Ymd').'-'.Str::upper(Str::substr((string) Str::ulid(), -8));
    }
}
