@foreach($products as $product)
<tr>
    <td class="ps-4" style="width: 80px;">
@if($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image))
    <a href="{{ asset('storage/' . $product->image) }}" target="_blank" title="Open image">
        <img src="{{ asset('storage/' . $product->image) }}" 
             alt="Product Image" 
             class="img-thumbnail" 
             style="width: 60px; height: 60px; object-fit: cover;">
    </a>
@elseif($product->image)
    <a href="{{ asset('storage/' . $product->image) }}" target="_blank" class="d-inline-block" title="Open image">
        <div class="bg-light d-flex align-items-center justify-content-center text-muted border rounded" style="width: 60px; height: 60px;">
            <i class="fa-solid fa-link me-1"></i>Open
        </div>
    </a>
@else
    <div class="bg-light d-flex align-items-center justify-content-center text-muted border rounded" style="width: 60px; height: 60px;">
        <i class="fa-solid fa-image"></i>
    </div>
@endif
    </td>
    <td>{{ $product->code }}</td>
    <td class="fw-bold text-dark">{{ $product->name }}</td>
    <td>{{ $product->category->name ?? 'N/A' }}</td>
    <td>{{ $product->brand->name ?? 'N/A' }}</td>
    <td class="fw-bold text-success">₱{{ number_format($product->price, 2) }}</td>
    <td class="fw-bold">{{ $product->quantity }}</td>
    <td>
        @if($product->quantity <= 0)
            <span class="badge bg-danger">Out of Stock</span>
        @elseif($product->quantity <= $product->low_stock_threshold)
            <span class="badge bg-danger">Low Stock</span>
        @elseif($product->quantity >= $product->overstock_threshold)
            <span class="badge bg-warning text-dark">Overstock</span>
        @else
            <span class="badge bg-success">Good Stock</span>
        @endif
    </td>
    <td class="ps-3">
        <div class="dropdown">
            <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport">
                <i class="fa-solid fa-gear"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#ajax-editProductModal{{ $product->id }}">
                        <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#ajax-stockInModal{{ $product->id }}">
                        <i class="fa-solid fa-arrow-down me-2 text-success"></i>Stock In
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#ajax-damagedModal{{ $product->id }}">
                        <i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>Report Damaged
                    </button>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#ajax-deleteModal{{ $product->id }}">
                        <i class="fa-solid fa-trash me-2"></i>Delete
                    </button>
                </li>
            </ul>
        </div>
    </td>
</tr>
@endforeach
