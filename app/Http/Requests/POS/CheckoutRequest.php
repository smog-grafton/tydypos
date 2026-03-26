<?php

namespace App\Http\Requests\POS;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'payments' => ['nullable', 'array'],
            'payments.*.method' => ['required_with:payments.*.amount', Rule::in(['cash', 'card', 'mobile_money'])],
            'payments.*.amount' => ['nullable', 'numeric', 'gte:0'],
            'payments.*.reference' => ['nullable', 'string', 'max:120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $items = array_values(array_filter($this->input('items', []), function (array $item): bool {
            return (float) ($item['quantity'] ?? 0) > 0;
        }));

        $payments = array_values(array_filter($this->input('payments', []), function (array $payment): bool {
            return isset($payment['method']) || (float) ($payment['amount'] ?? 0) > 0;
        }));

        $this->merge([
            'items' => $items,
            'payments' => $payments,
        ]);
    }
}
