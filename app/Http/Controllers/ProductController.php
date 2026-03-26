<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\StoreProductRequest;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Modules\Inventory\Actions\AdjustStockAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $branch = $this->resolveBranch();

        $products = Product::query()
            ->with('category')
            ->with(['stocks' => fn ($query) => $query->where('branch_id', $branch->id)])
            ->orderBy('name')
            ->get();

        return view('products.index', [
            'branch' => $branch,
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'products' => $products,
            'recentMovements' => \App\Models\StockMovement::query()
                ->with('product')
                ->where('branch_id', $branch->id)
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }

    public function store(StoreProductRequest $request, AdjustStockAction $adjustStockAction): RedirectResponse
    {
        $branch = $this->resolveBranch();
        $validated = $request->validated();
        $slug = Str::slug($validated['name']);

        $product = Product::query()->create([
            'category_id' => $validated['category_id'] ?? null,
            'sku' => $validated['sku'],
            'barcode' => $validated['barcode'] ?? null,
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($slug),
            'unit_name' => $validated['unit_name'],
            'price' => round((float) $validated['price'], 2),
            'cost' => round((float) $validated['cost'], 2),
            'tax_rate' => round((float) ($validated['tax_rate'] ?? 0), 2),
            'track_stock' => $request->boolean('track_stock', true),
            'allow_negative_stock' => $request->boolean('allow_negative_stock'),
            'is_active' => true,
        ]);

        ProductStock::query()->create([
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 0,
        ]);

        $initialStock = round((float) ($validated['initial_stock'] ?? 0), 3);

        if ($initialStock > 0) {
            $adjustStockAction->execute($product, $request->user(), [
                'branch_id' => $branch->id,
                'quantity_delta' => $initialStock,
                'type' => 'opening_stock',
                'notes' => 'Initial stock from product creation',
            ]);
        }

        return redirect()->route('products.index')->with('status', 'Product created successfully.');
    }

    protected function resolveBranch(): Branch
    {
        return auth()->user()->currentBranch ?? Branch::query()->firstOrFail();
    }

    protected function uniqueSlug(string $slug): string
    {
        $base = $slug !== '' ? $slug : Str::lower(Str::random(8));
        $candidate = $base;
        $counter = 2;

        while (Product::query()->where('slug', $candidate)->exists()) {
            $candidate = "{$base}-{$counter}";
            $counter++;
        }

        return $candidate;
    }
}
