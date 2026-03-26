<x-layouts.app :title="'Products'">
    <div class="shell">
        <nav class="nav">
            <div class="brand">
                <strong>Product & Inventory Control</strong>
                <span>{{ $branch->name }} branch catalog and stock adjustments</span>
            </div>

            <div class="actions">
                <a class="button button-muted" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="button button-muted" href="{{ route('pos.index') }}">Open POS</a>
            </div>
        </nav>

        <section class="hero">
            <div class="grid-2">
                <div class="panel">
                    <div class="muted">Create product</div>
                    <form method="POST" action="{{ route('products.store') }}" style="margin-top: 18px;">
                        @csrf

                        <div class="field">
                            <label for="name">Product name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                        </div>

                        <div class="field">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id">
                                <option value="">Uncategorized</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid-2" style="margin-top: 0;">
                            <div class="field">
                                <label for="sku">SKU</label>
                                <input id="sku" name="sku" type="text" value="{{ old('sku') }}" required>
                            </div>
                            <div class="field">
                                <label for="barcode">Barcode</label>
                                <input id="barcode" name="barcode" type="text" value="{{ old('barcode') }}">
                            </div>
                        </div>

                        <div class="grid-2" style="margin-top: 0;">
                            <div class="field">
                                <label for="price">Price</label>
                                <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', '0.00') }}" required>
                            </div>
                            <div class="field">
                                <label for="cost">Cost</label>
                                <input id="cost" name="cost" type="number" min="0" step="0.01" value="{{ old('cost', '0.00') }}" required>
                            </div>
                        </div>

                        <div class="grid-2" style="margin-top: 0;">
                            <div class="field">
                                <label for="tax_rate">Tax rate %</label>
                                <input id="tax_rate" name="tax_rate" type="number" min="0" max="100" step="0.01" value="{{ old('tax_rate', '0') }}">
                            </div>
                            <div class="field">
                                <label for="initial_stock">Initial stock</label>
                                <input id="initial_stock" name="initial_stock" type="number" min="0" step="0.001" value="{{ old('initial_stock', '0') }}">
                            </div>
                        </div>

                        <div class="grid-2" style="margin-top: 0;">
                            <div class="field">
                                <label for="unit_name">Unit name</label>
                                <input id="unit_name" name="unit_name" type="text" value="{{ old('unit_name', 'pcs') }}" required>
                            </div>
                            <div class="stack" style="justify-content: center; padding-top: 26px;">
                                <label class="checkbox"><input type="checkbox" name="track_stock" value="1" checked> Track stock</label>
                                <label class="checkbox"><input type="checkbox" name="allow_negative_stock" value="1"> Allow negative stock</label>
                            </div>
                        </div>

                        <button class="button" type="submit">Create product</button>
                    </form>
                </div>

                <div class="panel">
                    <div class="muted">Recent stock movements</div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Delta</th>
                                <th>After</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentMovements as $movement)
                                <tr>
                                    <td>{{ $movement->product?->name ?? 'Deleted product' }}</td>
                                    <td>{{ $movement->type }}</td>
                                    <td>{{ $movement->quantity_delta }}</td>
                                    <td>{{ $movement->quantity_after }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="muted">No stock movements yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel" style="margin-top: 18px;">
                <div class="muted">Catalog for {{ $branch->name }}</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Adjust inventory</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            @php $stock = $product->stocks->first(); @endphp
                            <tr>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    <div class="muted">{{ $product->category?->name ?? 'Uncategorized' }}</div>
                                </td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ number_format((float) $product->price, 2) }}</td>
                                <td>{{ number_format((float) ($stock?->quantity ?? 0), 3) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('inventory.adjustments.store') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: end;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div>
                                            <label>Delta</label>
                                            <input name="quantity_delta" type="number" step="0.001" style="min-width: 110px;" required>
                                        </div>
                                        <div>
                                            <label>Type</label>
                                            <select name="type">
                                                <option value="adjustment_in">In</option>
                                                <option value="adjustment_out">Out</option>
                                                <option value="opening_stock">Opening</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label>Note</label>
                                            <input name="notes" type="text" style="min-width: 180px;">
                                        </div>
                                        <button class="button button-muted" type="submit">Post</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
