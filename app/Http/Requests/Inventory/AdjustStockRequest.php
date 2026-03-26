<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class AdjustStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity_delta' => ['required', 'numeric', 'not_in:0'],
            'type' => ['required', 'in:opening_stock,adjustment_in,adjustment_out'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
