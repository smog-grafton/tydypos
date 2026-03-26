<x-layouts.app :title="'POS Checkout'">
    <div class="shell">
        <nav class="nav">
            <div class="brand">
                <strong>POS Checkout</strong>
                <span>{{ $branch->name }} branch live selling screen</span>
            </div>

            <div class="actions">
                <a class="button button-muted" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="button button-muted" href="{{ route('products.index') }}">Products</a>
            </div>
        </nav>

        <section class="hero">
            <div class="grid-2">
                <div class="panel">
                    <div class="muted">Create sale</div>

                    <form method="POST" action="{{ route('pos.checkout') }}" style="margin-top: 18px;">
                        @csrf

                        <div class="field">
                            <label for="customer_name">Customer name</label>
                            <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name') }}">
                        </div>

                        <div class="field">
                            <label for="notes">Sale note</label>
                            <input id="notes" name="notes" type="text" value="{{ old('notes') }}">
                        </div>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $index => $product)
                                    @php $stock = $product->stocks->first(); @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            <div class="muted">{{ $product->sku }}</div>
                                        </td>
                                        <td>{{ number_format((float) $product->price, 2) }}</td>
                                        <td>{{ number_format((float) ($stock?->quantity ?? 0), 3) }}</td>
                                        <td>
                                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $product->id }}">
                                            <input name="items[{{ $index }}][quantity]" type="number" min="0" step="0.001" value="{{ old("items.$index.quantity", '0') }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="grid-2">
                            <div class="field">
                                <label for="payments_0_method">Primary payment</label>
                                <select id="payments_0_method" name="payments[0][method]">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="mobile_money">Mobile money</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="payments_0_amount">Amount tendered</label>
                                <input id="payments_0_amount" name="payments[0][amount]" type="number" min="0" step="0.01" value="{{ old('payments.0.amount', '0.00') }}">
                            </div>
                        </div>

                        <div class="field">
                            <label for="payments_0_reference">Reference</label>
                            <input id="payments_0_reference" name="payments[0][reference]" type="text" value="{{ old('payments.0.reference') }}">
                        </div>

                        <button class="button" type="submit">Complete checkout</button>
                    </form>
                </div>

                <div class="stack">
                    <div class="panel">
                        <div class="muted">Checkout rules now active</div>
                        <ul style="padding-left: 18px; margin: 14px 0 0; line-height: 1.8;">
                            <li>Active products only</li>
                            <li>Inventory deduction inside one DB transaction</li>
                            <li>Sale items and payments stored as snapshots</li>
                            <li>Stock movement ledger written for each deducted line</li>
                        </ul>
                    </div>

                    <div class="panel">
                        <div class="muted">Recent sales</div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sale</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSales as $sale)
                                    <tr>
                                        <td>
                                            <strong>{{ $sale->sale_number }}</strong>
                                            <div class="muted">{{ $sale->items->count() }} items</div>
                                        </td>
                                        <td>{{ number_format((float) $sale->grand_total, 2) }}</td>
                                        <td>{{ $sale->payment_status }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="muted">No sales recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
