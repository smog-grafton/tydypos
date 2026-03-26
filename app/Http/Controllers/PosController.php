<?php

namespace App\Http\Controllers;

use App\Http\Requests\POS\CheckoutRequest;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Modules\POS\Actions\ProcessCheckoutAction;
use App\Modules\POS\Data\CheckoutData;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $branch = auth()->user()->currentBranch ?? Branch::query()->firstOrFail();

        $products = Product::query()
            ->where('is_active', true)
            ->with('category')
            ->with(['stocks' => fn ($query) => $query->where('branch_id', $branch->id)])
            ->orderBy('name')
            ->get();

        return view('pos.index', [
            'branch' => $branch,
            'products' => $products,
            'recentSales' => Sale::query()
                ->where('branch_id', $branch->id)
                ->with('items')
                ->latest()
                ->take(6)
                ->get(),
        ]);
    }

    public function store(CheckoutRequest $request, ProcessCheckoutAction $processCheckoutAction): RedirectResponse
    {
        $branch = $request->user()->currentBranch ?? Branch::query()->firstOrFail();

        $sale = $processCheckoutAction->execute(
            CheckoutData::fromArray($branch->id, $request->user()->id, $request->validated()),
        );

        return redirect()
            ->route('pos.index')
            ->with('status', "Checkout completed for {$sale->sale_number}.");
    }
}
