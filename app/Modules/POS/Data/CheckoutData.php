<?php

namespace App\Modules\POS\Data;

class CheckoutData
{
    /**
     * @param  array<int, array{product_id:int, quantity:numeric-string|int|float}>  $items
     * @param  array<int, array{method:string, amount:numeric-string|int|float, reference:?string}>  $payments
     */
    public function __construct(
        public readonly int $branchId,
        public readonly int $userId,
        public readonly array $items,
        public readonly array $payments,
        public readonly ?string $customerName,
        public readonly ?string $notes,
    ) {
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromArray(int $branchId, int $userId, array $validated): self
    {
        return new self(
            branchId: $branchId,
            userId: $userId,
            items: array_values($validated['items']),
            payments: array_values(array_filter($validated['payments'] ?? [], function (array $payment): bool {
                return (float) ($payment['amount'] ?? 0) > 0;
            })),
            customerName: $validated['customer_name'] ?? null,
            notes: $validated['notes'] ?? null,
        );
    }
}
