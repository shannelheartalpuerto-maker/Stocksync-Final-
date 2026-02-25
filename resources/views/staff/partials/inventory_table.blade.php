<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th>Image</th>
                <th>Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td style="width: 80px;">
    @if($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" 
             alt="Product Image" 
             class="img-thumbnail" 
             style="width: 60px; height: 60px; object-fit: cover;"
             onerror="this.parentNode.innerHTML='<div class=\'bg-light d-flex align-items-center justify-content-center text-muted border rounded\' style=\'width: 60px; height: 60px;\'><i class=\'fa-solid fa-image\'></i></div>'">
    @else
                        <div class="bg-light d-flex align-items-center justify-content-center text-muted border rounded" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-image"></i>
                        </div>
                    @endif
                </td>
                <td>{{ $product->code }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? 'N/A' }}</td>
                <td>{{ $product->brand->name ?? 'N/A' }}</td>
                <td>₱{{ number_format($product->price, 2) }}</td>
                <td class="fw-bold">{{ $product->quantity }}</td>
                <td>
                    @if($product->quantity <= 0)
                        <span class="badge bg-danger">Out of Stock</span>
                    @elseif($product->quantity < $product->low_stock_threshold)
                        <span class="badge bg-danger">Low Stock</span>
                    @elseif($product->quantity > $product->overstock_threshold)
                        <span class="badge bg-warning text-dark">Overstock</span>
                    @else
                        <span class="badge bg-success">Good Stock</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reportDamageModal{{ $product->id }}">
                        <i class="fa-solid fa-triangle-exclamation"></i> Report Damage
                    </button>
                </td>
            </tr>
            @endforeach
            @if($products->isEmpty())
            <tr>
                <td colspan="8" class="text-center">No products found.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
{{ $products->links() }}

@foreach($products as $product)
<div class="modal fade" id="reportDamageModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('staff.inventory.damage', $product->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Report Damaged Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <small>Reporting damage for <strong>{{ $product->name }}</strong>. This will deduct from available stock.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity Damaged</label>
                        <input type="number" class="form-control" name="quantity" min="1" max="{{ $product->quantity }}" required>
                        <small class="text-muted">Max available: {{ $product->quantity }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes / Reason</label>
                        <textarea class="form-control" name="notes" placeholder="Describe the damage or reason..." required rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
