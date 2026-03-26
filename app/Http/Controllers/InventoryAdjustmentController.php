<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\AdjustStockRequest;
use App\Models\Branch;
use App\Models\Product;
use App\Modules\Inventory\Actions\AdjustStockAction;
use Illuminate\Http\RedirectResponse;

class InventoryAdjustmentController extends Controller
{
    public function store(AdjustStockRequest $request, AdjustStockAction $adjustStockAction): RedirectResponse
    {
        $branch = auth()->user()->currentBranch ?? Branch::query()->firstOrFail();
        $product = Product::query()->findOrFail($request->integer('product_id'));

        $adjustStockAction->execute($product, $request->user(), [
            'branch_id' => $branch->id,
            'quantity_delta' => $request->input('quantity_delta'),
            'type' => $request->string('type')->toString(),
            'notes' => $request->input('notes'),
        ]);

        return redirect()->route('products.index')->with('status', 'Inventory adjusted successfully.');
    }
}
