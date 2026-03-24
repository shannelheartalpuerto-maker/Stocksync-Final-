<div class="table-responsive animate-fade-up">
    <table class="table align-middle mb-0 inv-table">
        <thead>
            <tr>
                <th class="ps-4">Product</th>
                <th>Category / Brand</th>
                <th>Price</th>
                <th>Stock Level</th>
                <th>Status</th>
                <th class="text-end pe-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td class="ps-4 staff-product-cell">
                    <div class="d-flex align-items-center staff-product-main">
                        <div class="product-img-wrapper me-3 staff-product-thumb">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="rounded-3 shadow-sm border" 
                                     style="width: 52px; height: 52px; object-fit: cover;"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="bg-light align-items-center justify-content-center text-muted border rounded-3" style="width: 52px; height: 52px; display:none;">
                                    <i class="fa-solid fa-image small"></i>
                                </div>
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center text-muted border rounded-3" style="width: 52px; height: 52px;">
                                    <i class="fa-solid fa-image small"></i>
                                </div>
                            @endif
                        </div>
                        <div class="staff-product-meta">
                            <div class="fw-bold text-dark fs-6 staff-product-name">{{ $product->name }}</div>
                            <div class="text-muted small code-font opacity-75 staff-product-code">{{ $product->code }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="badge bg-soft-info text-primary mb-1 fw-600 px-2 py-1">{{ $product->category->name ?? 'Uncategorized' }}</div>
                    <div class="text-muted small fw-500">{{ $product->brand->name ?? 'No Brand' }}</div>
                </td>
                <td>
                    <span class="fw-bold text-dark">₱{{ number_format($product->price, 2) }}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold fs-5 {{ $product->quantity <= $product->low_stock_threshold ? 'text-danger' : 'text-dark' }}">
                            {{ number_format($product->quantity) }}
                        </span>
                        @if($product->quantity <= $product->low_stock_threshold)
                            <i class="fa-solid fa-arrow-trend-down text-danger small"></i>
                        @endif
                    </div>
                    <div class="progress rounded-pill" style="height: 6px; width: 80px; background-color: #f1f5f9;">
                        @php
                            $percentage = min(100, ($product->quantity / max(1, $product->overstock_threshold)) * 100);
                            $color = 'bg-success';
                            if($product->quantity <= $product->low_stock_threshold) $color = 'bg-danger';
                            elseif($product->quantity >= $product->overstock_threshold) $color = 'bg-warning';
                        @endphp
                        <div class="progress-bar {{ $color }} rounded-pill" role="progressbar" style="width: {{ $percentage }}%"></div>
                    </div>
                </td>
                <td>
                    @if($product->quantity <= 0)
                        <span class="badge bg-soft-danger px-3 py-2 rounded-pill fw-600">Out of Stock</span>
                    @elseif($product->quantity <= $product->low_stock_threshold)
                        <span class="badge bg-soft-danger px-3 py-2 rounded-pill fw-600">Low Stock</span>
                    @elseif($product->quantity >= $product->overstock_threshold)
                        <span class="badge bg-soft-warning px-3 py-2 rounded-pill fw-600">Overstock</span>
                    @else
                        <span class="badge bg-soft-success px-3 py-2 rounded-pill fw-600">Good Stock</span>
                    @endif
                </td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-600" data-bs-toggle="modal" data-bs-target="#reportDamageModal{{ $product->id }}">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Damage
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="text-muted opacity-50">
                        <i class="fa-solid fa-magnifying-glass fs-1 mb-3 d-block"></i>
                        <p class="h6 fw-bold">No products match your criteria.</p>
                        <p class="small">Try adjusting your filters or search terms.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $products->links() }}
</div>

@foreach($products as $product)
<div class="modal smooth-modal" id="reportDamageModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('staff.inventory.damage', $product->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Report Damage</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3">
                        <div class="me-3">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-white border rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fa-solid fa-image text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $product->name }}</div>
                            <div class="text-muted small">Current Stock: <strong>{{ $product->quantity }}</strong></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Quantity Damaged</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-minus text-danger"></i></span>
                            <input type="number" class="form-control" name="quantity" min="1" max="{{ $product->quantity }}" required placeholder="0">
                        </div>
                        <div class="form-text text-danger small"><i class="fa-solid fa-circle-info me-1"></i> This quantity will be permanently removed.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600">Reason / Notes</label>
                        <textarea class="form-control" name="notes" placeholder="Explain what happened (e.g., Broken during display, Expired...)" required rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 rounded-3 shadow-sm">
                        Confirm & Deduct
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
