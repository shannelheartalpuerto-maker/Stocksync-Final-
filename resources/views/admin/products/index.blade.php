@extends('layouts.app')

@section('content')
<link href="{{ asset('css/staff-design.css') }}?v={{ time() }}" rel="stylesheet">
<style>
/* ── Inventory Page Styles ── */

.inv-topbar {
    position: relative;
    overflow: hidden;
    border-radius: 18px;
    padding: 1.1rem 1.35rem;
    background: linear-gradient(130deg, #0f766e 0%, #0ea5e9 55%, #2563eb 100%);
    border: 1px solid rgba(255, 255, 255, 0.24);
    box-shadow: 0 10px 26px rgba(14, 116, 144, 0.18);
}
.inv-topbar::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 15% 20%, rgba(255,255,255,.20) 0, rgba(255,255,255,0) 32%),
        radial-gradient(circle at 90% 0%, rgba(255,255,255,.14) 0, rgba(255,255,255,0) 34%);
    pointer-events: none;
}
.inv-topbar-inner {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.9rem;
    flex-wrap: wrap;
}
.inv-title-wrap {
    display: flex;
    align-items: center;
    gap: 0.85rem;
}
.inv-title-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,.18);
    color: #fff;
    font-size: 1.05rem;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
}
.inv-title-text {
    font-size: 1.95rem;
    font-weight: 750;
    letter-spacing: -0.35px;
    color: #fff;
    line-height: 1.05;
    margin: 0;
}
.inv-header-actions {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
}
.inv-action-btn {
    border-radius: 999px;
    padding: 0.52rem 1rem;
    font-weight: 650;
    font-size: 0.9rem;
}
.inv-action-ghost {
    background: rgba(255,255,255,.16);
    color: #fff;
    border: 1px solid rgba(255,255,255,.42);
}
.inv-action-ghost:hover {
    background: rgba(255,255,255,.24);
    color: #fff;
}
.inv-action-solid {
    background: #fff;
    color: #1d4ed8;
    border: 1px solid #fff;
    box-shadow: 0 6px 14px rgba(0,0,0,.14);
}
.inv-action-solid:hover {
    background: #f8fbff;
    color: #1e40af;
}

/* Action dropdown */
.action-menu-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1.5px solid #e5e7eb;
    background: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    transition: all .2s ease;
    cursor: pointer;
    padding: 0;
}
.action-menu-btn:hover, .action-menu-btn:focus, .show > .action-menu-btn {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #fff;
    box-shadow: 0 4px 12px rgba(79,70,229,.3);
    outline: none;
}
.action-dropdown-menu {
    border: none;
    border-radius: 14px;
    box-shadow: 0 8px 30px rgba(0,0,0,.13);
    min-width: 185px;
    padding: 0.4rem;
    overflow: hidden;
}
.action-dropdown-menu .dropdown-item {
    border-radius: 9px;
    padding: 0.58rem 0.85rem;
    font-size: 0.86rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.65rem;
    transition: background .15s;
    color: #374151;
}
.action-dropdown-menu .dropdown-item:hover { background: #f3f4f6; color: #111827; }
.action-dropdown-menu .dropdown-item.item-danger { color: #dc2626; }
.action-dropdown-menu .dropdown-item.item-danger:hover { background: #fee2e2; color: #b91c1c; }
.action-dropdown-menu .dropdown-divider { margin: 0.3rem 0; opacity: .4; }
.action-icon-wrap {
    width: 26px; height: 26px;
    border-radius: 7px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    flex-shrink: 0;
}
/* Improved table */
.inv-table thead th {
    background: #f8faff;
    font-size: 0.71rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #6b7280;
    border-bottom: 2px solid #e5e7eb;
    padding: 0.85rem 1rem;
    white-space: nowrap;
}
.inv-table td {
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}
.inv-table tbody tr:last-child td { border-bottom: none; }
.inv-table tbody tr:hover { background: #fafbff; }
.prod-thumb {
    width: 44px; height: 44px;
    border-radius: 10px;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.prod-thumb-placeholder {
    width: 44px; height: 44px;
    border-radius: 10px;
    background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    color: #d1d5db; font-size: 1rem;
}
.sku-badge {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 0.2rem 0.5rem;
    font-size: 0.73rem;
    font-weight: 600;
    color: #374151;
    font-family: monospace;
}
</style>

<div class="staff-container animate-fade-up">

    {{-- Page Header --}}
    <div class="inv-topbar mb-4">
        <div class="inv-topbar-inner">
            <div class="inv-title-wrap">
                <span class="inv-title-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
                <h5 class="inv-title-text">Inventory Management</h5>
            </div>
            <div class="inv-header-actions">
                <a href="{{ route('admin.reports.restock_list') }}" target="_blank"
                   class="btn btn-sm inv-action-btn inv-action-ghost">
                    <i class="fa-solid fa-print me-1"></i>Restock List
                </a>
                <button type="button"
                        class="btn btn-sm inv-action-btn inv-action-solid"
                        data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fa-solid fa-plus me-1"></i>Add Product
                </button>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-body-custom">
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                </div>
            @endif

            <!-- Filter Section -->
            <div class="filter-section mb-4">
                <form action="{{ route('admin.products.index') }}" method="GET" id="productsFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <div class="search-input-group">
                                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                <input type="text" name="search" class="form-control search-control" placeholder="Search by name or SKU..." value="{{ request('search') }}">
                                @if(request('search'))
                                    <a href="{{ route('admin.products.index', request()->except('search')) }}" class="btn-clear-search">
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select border-0 shadow-sm bg-light">
                                <option value="all">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="brand" class="form-select border-0 shadow-sm bg-light">
                                <option value="all">All Brands</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select border-0 shadow-sm bg-light">
                                <option value="all">All Status</option>
                                <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                                <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                                <option value="good" {{ request('status') == 'good' ? 'selected' : '' }}>Good Stock</option>
                                <option value="over" {{ request('status') == 'over' ? 'selected' : '' }}>Overstock</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-indigo flex-grow-1">
                                    <i class="fa-solid fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-light" title="Reset Filters">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle inv-table mb-0">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem;">Product</th>
                            <th>SKU / Code</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        @foreach($products as $product)
                        <tr>
                            <td style="padding-left:1.5rem;">
                                <div class="d-flex align-items-center gap-3">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="" class="prod-thumb">
                                    @else
                                        <div class="prod-thumb-placeholder">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold text-dark" style="font-size:.9rem;">{{ $product->name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">ID&nbsp;#{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="sku-badge">{{ $product->code }}</span></td>
                            <td><span class="text-secondary" style="font-size:.875rem;">{{ $product->category->name ?? 'N/A' }}</span></td>
                            <td><span class="text-secondary" style="font-size:.875rem;">{{ $product->brand->name ?? 'N/A' }}</span></td>
                            <td><span class="fw-semibold text-dark">₱{{ number_format($product->price, 2) }}</span></td>
                            <td>
                                <span class="fw-bold {{ $product->quantity <= $product->low_stock_threshold ? 'text-danger' : 'text-dark' }}">
                                    {{ $product->quantity }}
                                </span>
                            </td>
                            <td>
                                @if($product->quantity <= 0)
                                    <span class="status-badge status-danger"><i class="fa-solid fa-circle-xmark me-1"></i>Out of Stock</span>
                                @elseif($product->quantity <= $product->low_stock_threshold)
                                    <span class="status-badge status-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i>Low Stock</span>
                                @elseif($product->quantity >= $product->overstock_threshold)
                                    <span class="status-badge status-info"><i class="fa-solid fa-arrow-up me-1"></i>Overstock</span>
                                @else
                                    <span class="status-badge status-success"><i class="fa-solid fa-circle-check me-1"></i>Good Stock</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="action-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu action-dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button type="button" class="dropdown-item"
                                                data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                                                <span class="action-icon-wrap" style="background:#e0e7ff;">
                                                    <i class="fa-solid fa-pen text-primary"></i>
                                                </span>
                                                Edit Product
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item"
                                                data-bs-toggle="modal" data-bs-target="#stockInModal{{ $product->id }}">
                                                <span class="action-icon-wrap" style="background:#dcfce7;">
                                                    <i class="fa-solid fa-arrow-down text-success"></i>
                                                </span>
                                                Stock In
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item"
                                                data-bs-toggle="modal" data-bs-target="#damagedModal{{ $product->id }}">
                                                <span class="action-icon-wrap" style="background:#fef3c7;">
                                                    <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                                </span>
                                                Report Damage
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button type="button" class="dropdown-item item-danger"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}">
                                                <span class="action-icon-wrap" style="background:#fee2e2;">
                                                    <i class="fa-solid fa-trash text-danger"></i>
                                                </span>
                                                Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @if($products->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fa-solid fa-box-open fa-3x mb-3 text-muted opacity-25"></i>
                                    <h6 class="text-muted">No products found matching your criteria</h6>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-link text-decoration-none">Clear all filters</a>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer-custom bg-white" id="productsPagination">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} entries
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        </div>
        <div id="ajaxModalsMount"></div>
    </div>
</div>

@push('modals')
<!-- Modals for each product -->
@foreach($products as $product)
    <!-- Stock In Modal -->
    <div class="modal fade" id="stockInModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('admin.products.stock_in', $product->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="fa-solid fa-arrow-down text-success fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold">Stock In</h5>
                                <p class="text-muted small mb-0">{{ $product->name }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Quantity to Add</label>
                            <div class="input-group search-input-group border">
                                <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-plus text-success"></i></span>
                                <input type="number" class="form-control border-0 bg-transparent" name="quantity" min="1" placeholder="0" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark">Notes (Optional)</label>
                            <textarea class="form-control bg-light border-0" name="notes" rows="3" placeholder="Add supplier info, invoice #, etc."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4">Update Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Damaged Report Modal -->
    <div class="modal fade" id="damagedModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('admin.products.damaged', $product->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="fa-solid fa-triangle-exclamation text-warning fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold">Report Damage</h5>
                                <p class="text-muted small mb-0">{{ $product->name }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Quantity Damaged</label>
                            <div class="input-group search-input-group border">
                                <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-hashtag text-warning"></i></span>
                                <input type="number" class="form-control border-0 bg-transparent" name="quantity" min="1" max="{{ $product->quantity }}" required>
                            </div>
                            <small class="text-muted">Available stock: {{ $product->quantity }}</small>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark">Reason/Notes</label>
                            <textarea class="form-control bg-light border-0" name="notes" rows="3" placeholder="Describe the damage..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4">Report Damage</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <div class="bg-danger bg-opacity-10 d-inline-flex p-3 rounded-circle mb-3">
                            <i class="fa-solid fa-trash-can text-danger fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Delete Product?</h5>
                        <p class="text-muted small mb-0">This action cannot be undone. Are you sure you want to delete <strong>{{ $product->name }}</strong>?</p>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <button type="button" class="btn btn-light px-4 flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 px-4">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="fa-solid fa-pen-to-square text-primary fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0">Edit Product</h5>
                                <p class="text-muted small mb-0">{{ $product->name }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="row g-3">
                            <!-- Left Column -->
                            <div class="col-md-5">
                                <div class="bg-light rounded-4 p-3 text-center h-100">
                                    <label class="form-label fw-bold text-dark mb-2">Product Image</label>
                                    
                                    <div class="mb-2" style="height:140px;overflow:hidden;position:relative;border-radius:.75rem;background:white;border:2px dashed #e2e8f0;">
                                        <img id="editImagePreview{{ $product->id }}" src="{{ $product->image ? asset('storage/' . $product->image) : '#' }}" alt="Preview" style="display: {{ $product->image ? 'block' : 'none' }}; width: 100%; height: 100%; object-fit: cover;">
                                        <div id="editImagePlaceholder{{ $product->id }}" class="text-muted d-flex flex-column align-items-center justify-content-center h-100" style="display: {{ $product->image ? 'none' : 'flex' }} !important;">
                                            <i class="fa-solid fa-cloud-arrow-up fa-2x mb-1 opacity-25"></i>
                                            <p class="small mb-0">Click to upload</p>
                                        </div>
                                        <input type="file" class="form-control" name="image" accept="image/*" id="editImageInput{{ $product->id }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" onchange="previewEditImage(this, '{{ $product->id }}')">
                                    </div>

                                    <div class="text-start mt-2">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Category</label>
                                            <select class="form-select border-0 bg-white shadow-sm" name="category_id" required>
                                                @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Brand</label>
                                            <select class="form-select border-0 bg-white shadow-sm" name="brand_id">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-7">
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Product Name</label>
                                    <input type="text" class="form-control border-0 bg-light" name="name" value="{{ $product->name }}" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold">SKU / Barcode</label>
                                    <div class="input-group search-input-group border">
                                        <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-barcode text-muted"></i></span>
                                        <input type="text" class="form-control border-0 bg-transparent" name="code" id="editProductCode{{ $product->id }}" value="{{ $product->code }}" required>
                                        <button class="btn btn-indigo btn-sm my-1 me-1 rounded-3 btn-choose-barcode-file-edit" type="button" data-target-input="editProductCode{{ $product->id }}">
                                            <i class="fa-solid fa-camera me-1"></i> Scan
                                        </button>
                                    </div>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Price</label>
                                        <div class="input-group search-input-group border">
                                            <span class="input-group-text bg-transparent border-0">₱</span>
                                            <input type="number" step="0.01" class="form-control border-0 bg-transparent" name="price" value="{{ $product->price }}" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Quantity</label>
                                        <div class="input-group search-input-group border">
                                            <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-cubes text-muted"></i></span>
                                            <input type="number" class="form-control border-0 bg-transparent" name="quantity" value="{{ $product->quantity }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-indigo bg-opacity-10 rounded-4 p-3">
                                    <h6 class="fw-bold mb-2 text-indigo"><i class="fa-solid fa-sliders me-2"></i>Inventory Thresholds</h6>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <label class="form-label small fw-bold text-muted mb-1">Low Stock</label>
                                            <input type="number" class="form-control border-0 shadow-sm threshold-input" name="low_stock_threshold" value="{{ $product->low_stock_threshold ?? 10 }}" min="0" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small fw-bold text-muted mb-1">Good Stock</label>
                                            <input type="number" class="form-control border-0 shadow-sm threshold-input" name="good_stock_threshold" value="{{ $product->good_stock_threshold ?? 50 }}" min="0" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small fw-bold text-muted mb-1">Overstock</label>
                                            <input type="number" class="form-control border-0 shadow-sm threshold-input" name="overstock_threshold" value="{{ $product->overstock_threshold ?? 100 }}" min="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="addProductForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-indigo p-2 rounded-3 me-3 d-inline-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                            <i class="fa-solid fa-box-open text-white fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="addProductModalLabel">New Product</h5>
                            <p class="text-muted small mb-0">Fill in the details below to add a product</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    @if ($errors->any() && !old('_method'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 py-2">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <!-- Left Column -->
                        <div class="col-md-5">
                            <div class="bg-light rounded-4 p-3 text-center h-100">
                                <label class="form-label fw-bold text-dark mb-2">Product Image</label>
                                <div class="mb-2" style="height:140px;overflow:hidden;position:relative;border-radius:.75rem;background:white;border:2px dashed #e2e8f0;">
                                    <img id="addImagePreview" src="#" alt="Preview" style="display:none;width:100%;height:100%;object-fit:cover;">
                                    <div id="addImagePlaceholder" class="text-muted d-flex flex-column align-items-center justify-content-center h-100">
                                        <i class="fa-solid fa-cloud-arrow-up fa-2x mb-1 opacity-25"></i>
                                        <p class="small mb-0">Click to upload</p>
                                    </div>
                                    <input type="file" class="form-control" name="image" accept="image/*" id="addImageInput" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;">
                                </div>
                                <div class="text-start mt-2">
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Category <span class="text-danger">*</span></label>
                                        <select class="form-select border-0 bg-white shadow-sm" name="category_id" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Brand</label>
                                        <select class="form-select border-0 bg-white shadow-sm" name="brand_id">
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-7">
                            <div class="mb-2">
                                <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-0 bg-light" name="name" value="{{ old('name') }}" placeholder="e.g. Ballpen" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">SKU / Barcode <span class="text-danger">*</span></label>
                                <div class="input-group search-input-group border">
                                    <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-barcode text-muted"></i></span>
                                    <input type="text" class="form-control border-0 bg-transparent" name="code" id="addProductCode" value="{{ old('code') }}" placeholder="Scan or type code" required>
                                    <button class="btn btn-indigo btn-sm my-1 me-1 rounded-3" type="button" id="btn-choose-barcode-file">
                                        <i class="fa-solid fa-camera me-1"></i> Scan
                                    </button>
                                </div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold">Price <span class="text-danger">*</span></label>
                                    <div class="input-group search-input-group border">
                                        <span class="input-group-text bg-transparent border-0">₱</span>
                                        <input type="number" step="0.01" class="form-control border-0 bg-transparent" name="price" value="{{ old('price') }}" placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">Initial Stock <span class="text-danger">*</span></label>
                                    <div class="input-group search-input-group border">
                                        <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-cubes text-muted"></i></span>
                                        <input type="number" class="form-control border-0 bg-transparent" name="quantity" value="{{ old('quantity', 0) }}" placeholder="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border rounded-4 p-3">
                                <h6 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-sliders me-2 text-primary"></i>Inventory Thresholds</h6>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold text-dark mb-1">Low Stock</label>
                                        <input type="number" class="form-control border shadow-sm threshold-input text-dark bg-white" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" min="0" required>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold text-dark mb-1">Good Stock</label>
                                        <input type="number" class="form-control border shadow-sm threshold-input text-dark bg-white" name="good_stock_threshold" value="{{ old('good_stock_threshold', 50) }}" min="0" required>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold text-dark mb-1">Overstock</label>
                                        <input type="number" class="form-control border shadow-sm threshold-input text-dark bg-white" name="overstock_threshold" value="{{ old('overstock_threshold', 100) }}" min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-indigo px-4 fw-semibold">Create Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endpush

@push('scripts')
<script>
    // Cleanup any stuck backdrops on load
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we have ghost backdrops
        const backdrops = document.querySelectorAll('.modal-backdrop');
        if (backdrops.length > 0) {
            // Only remove if no modal is actually open (check by class)
            if (!document.querySelector('.modal.show')) {
                console.warn('Cleaning up stuck backdrops');
                backdrops.forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        }
    });

    // Global function for Edit Image Preview
    function previewEditImage(input, id) {
        const preview = document.getElementById('editImagePreview' + id);
        const placeholder = document.getElementById('editImagePlaceholder' + id);
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Product Index Script Loaded');

        @if ($errors->any() && !old('_method'))
            try {
                var addProductModal = new bootstrap.Modal(document.getElementById('addProductModal'));
                addProductModal.show();
            } catch(e) { console.error('Error opening error modal:', e); }
        @endif

        // Auto-open add product modal from URL param (?addProduct=1)
        if (new URLSearchParams(window.location.search).has('addProduct')) {
            try {
                var addModal = new bootstrap.Modal(document.getElementById('addProductModal'));
                addModal.show();
            } catch(e) {}
        }

        // Image Preview Logic
        const addImageInput = document.getElementById('addImageInput');
        const addImagePreview = document.getElementById('addImagePreview');
        const addImagePlaceholder = document.getElementById('addImagePlaceholder');

        if (addImageInput && addImagePreview && addImagePlaceholder) {
            addImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        addImagePreview.src = e.target.result;
                        addImagePreview.style.display = 'block';
                        addImagePlaceholder.style.display = 'none';
                    }
                    reader.readAsDataURL(file);
                } else {
                    addImagePreview.src = '#';
                    addImagePreview.style.display = 'none';
                    addImagePlaceholder.style.display = 'block';
                }
            });
        }

        // Scanner Variables
        let codeReader;
        let selectedDeviceId;
        let targetInputForScan = null;
        const scannerModalEl = document.getElementById('scannerModal');
        const scannerModal = new bootstrap.Modal(scannerModalEl);
        const sourceSelect = document.getElementById('sourceSelect');
        const statusText = document.getElementById('scannerStatus');

        // Function to open scanner
        function openScanner(inputElement) {
            targetInputForScan = inputElement;
            scannerModal.show();
        }

        // Initialize Scanner Logic
        scannerModalEl.addEventListener('show.bs.modal', function () {
             // Z-Index Fix: Simply set very high values
             scannerModalEl.style.zIndex = "100000";
             
             // Fix the backdrop that will be created
             setTimeout(() => {
                 const backdrops = document.querySelectorAll('.modal-backdrop');
                 if (backdrops.length > 0) {
                     // The last backdrop added corresponds to this modal
                     const lastBackdrop = backdrops[backdrops.length - 1];
                     lastBackdrop.style.zIndex = "99999";
                 }
             }, 10);

             if (typeof ZXing === 'undefined') {
                 statusText.textContent = "Scanner library not loaded.";
                 return;
             }
 
             codeReader = new ZXing.BrowserMultiFormatReader();
             statusText.textContent = "Starting camera...";
             
             codeReader.listVideoInputDevices()
                 .then((videoInputDevices) => {
                     sourceSelect.innerHTML = '';
                     if (videoInputDevices.length >= 1) {
                         videoInputDevices.forEach((element) => {
                             const sourceOption = document.createElement('option');
                             sourceOption.text = element.label;
                             sourceOption.value = element.deviceId;
                             sourceSelect.appendChild(sourceOption);
                         });
 
                         sourceSelect.onchange = () => {
                             selectedDeviceId = sourceSelect.value;
                             startScanning(selectedDeviceId);
                         };
 
                         if(videoInputDevices.length > 1) {
                             sourceSelect.style.display = 'block';
                         }
 
                         // Prefer back camera
                         const backCamera = videoInputDevices.find(device => device.label.toLowerCase().includes('back') || device.label.toLowerCase().includes('environment'));
                         selectedDeviceId = backCamera ? backCamera.deviceId : videoInputDevices[0].deviceId;
                         
                         sourceSelect.value = selectedDeviceId;
                         startScanning(selectedDeviceId);
                     } else {
                         statusText.textContent = "No camera found.";
                     }
                 })
                 .catch((err) => {
                     console.error(err);
                     statusText.textContent = "Error accessing camera: " + err;
                 });
        });

        scannerModalEl.addEventListener('hidden.bs.modal', function () {
            if (codeReader) {
                codeReader.reset();
                codeReader = null;
            }
            statusText.textContent = "Camera stopped.";
            targetInputForScan = null;
        });

        function startScanning(deviceId) {
            codeReader.decodeFromVideoDevice(deviceId, 'video', (result, err) => {
                if (result) {
                    console.log(result);
                    if(targetInputForScan) {
                        targetInputForScan.value = result.text;
                        // Visual feedback
                        targetInputForScan.style.backgroundColor = '#e8f5e9';
                        setTimeout(() => {
                            targetInputForScan.style.backgroundColor = '';
                        }, 500);
                    }
                    scannerModal.hide();
                }
            });
        }

        // Initialize Add Product Button
        const addChooseFileBtn = document.getElementById('btn-choose-barcode-file');
        const addProductCodeInput = document.getElementById('addProductCode');
        
        if (addChooseFileBtn && addProductCodeInput) {
            addChooseFileBtn.addEventListener('click', function() {
                openScanner(addProductCodeInput);
            });
        }

        // Initialize Edit Product Buttons
        document.querySelectorAll('.btn-choose-barcode-file-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetInputId = btn.dataset.targetInput;
                const textInput = document.getElementById(targetInputId);
                if (textInput) {
                    openScanner(textInput);
                }
            });
        });

        // Reset Add Product Form on Modal Open
        const addProductModalEl = document.getElementById('addProductModal');
        if (addProductModalEl) {
            addProductModalEl.addEventListener('show.bs.modal', function (event) {
                // Only reset if there are no validation errors displayed
                // If there are errors, we want to keep the user's input so they can fix it
                if (!addProductModalEl.querySelector('.alert-danger')) {
                    const form = document.getElementById('addProductForm');
                    if (form) {
                        form.reset();
                         // Reset Image Preview
                         if (addImagePreview && addImagePlaceholder) {
                             addImagePreview.src = '#';
                             addImagePreview.style.display = 'none';
                             addImagePlaceholder.style.display = 'flex';
                         }
                    }
                }
            });
        }

        // Prevent double submission for Add Product Form
        const addForm = document.getElementById('addProductForm');
        if (addForm) {
            addForm.addEventListener('submit', function() {
                const btn = addForm.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Creating...';
                }
            });
        }

        // Threshold Validation
        function validateThresholds(form) {
            const lowInput = form.querySelector('input[name="low_stock_threshold"]');
            const goodInput = form.querySelector('input[name="good_stock_threshold"]');
            const overInput = form.querySelector('input[name="overstock_threshold"]');
            
            if (!lowInput || !goodInput || !overInput) return true;

            const low = parseInt(lowInput.value) || 0;
            const good = parseInt(goodInput.value) || 0;
            const over = parseInt(overInput.value) || 0;
            
            // Reset validities
            lowInput.setCustomValidity('');
            goodInput.setCustomValidity('');
            overInput.setCustomValidity('');

            let isValid = true;

            if (low >= good) {
                goodInput.setCustomValidity('Good stock threshold must be greater than Low stock threshold (' + low + ').');
                isValid = false;
            }
            
            if (good >= over) {
                overInput.setCustomValidity('Overstock threshold must be greater than Good stock threshold (' + good + ').');
                isValid = false;
            }
            
            return isValid;
        }

        document.querySelectorAll('form').forEach(form => {
             const inputs = form.querySelectorAll('.threshold-input');
             if (inputs.length > 0) {
                 inputs.forEach(input => {
                     input.addEventListener('input', () => validateThresholds(form));
                 });
             }
             
             // Also validate on submit
             form.addEventListener('submit', (e) => {
                 if (!validateThresholds(form)) {
                     // Check if form is already submitting (to prevent double submission logic from conflicting)
                     // But actually if invalid, we want to stop it.
                     e.preventDefault();
                     e.stopPropagation();
                     form.reportValidity();
                     
                     // Re-enable submit button if it was disabled by other scripts
                     const btn = form.querySelector('button[type="submit"]');
                     if (btn) {
                         btn.disabled = false;
                         // Restore original text if possible (handling generic "Adding..." case)
                         if (btn.innerHTML.includes('Creating...')) {
                             btn.innerHTML = 'Create Product'; 
                         } else if (btn.innerHTML.includes('Save Changes')) {
                             // Edit modal doesn't have double submission script yet, but good to be safe
                         }
                     }
                 }
             });
        });
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // AJAX Inventory Filtering with light animations
        const form = document.getElementById('productsFilterForm');
        const searchBtn = document.getElementById('productsSearchBtn');
        const selects = form ? form.querySelectorAll('select[name="category"], select[name="brand"], select[name="status"]') : [];
        const tableBody = document.getElementById('productsTableBody');
        const pagination = document.getElementById('productsPagination');
        const modalsMount = document.getElementById('ajaxModalsMount');
        const mainContentCard = document.querySelector('.content-card'); // Target the main content card
        const animateOnce = (el) => {
            if (!el) return;
            el.classList.remove('animate-fade-up');
            void el.offsetWidth;
            el.classList.add('animate-fade-up');
            setTimeout(() => el.classList.remove('animate-fade-up'), 600);
        };
        const setLoading = (on) => {
            if (mainContentCard) mainContentCard.classList.toggle('is-loading', !!on);
            if (searchBtn) searchBtn.classList.toggle('loading', !!on);
        };

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        async function loadInventory(params) {
            setLoading(true);
            try {
                const url = '{{ route('admin.products.index') }}' + (params ? ('?' + params) : '');
                const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await resp.json();
                if (tableBody) tableBody.innerHTML = data.rows_html;
                if (pagination) {
                    pagination.innerHTML = data.pagination_html;
                    // Intercept clicks on pagination links
                    pagination.querySelectorAll('a').forEach(a => {
                        a.addEventListener('click', function(e) {
                            e.preventDefault();
                            const nextUrl = new URL(a.href);
                            loadInventory(nextUrl.searchParams.toString());
                        });
                    });
                }
                if (modalsMount) {
                    modalsMount.innerHTML = data.modals_html || '';
                }
                animateOnce(tableBody);
            } finally {
                setLoading(false);
            }
        }
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const params = new URLSearchParams(new FormData(form)).toString();
                loadInventory(params);
            });
            selects.forEach(sel => {
                sel.addEventListener('change', function() {
                    const params = new URLSearchParams(new FormData(form)).toString();
                    loadInventory(params);
                });
            });

            // Auto-search on input
            const searchInput = form.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', debounce(function() {
                    const params = new URLSearchParams(new FormData(form)).toString();
                    loadInventory(params);
                }, 500));
            }
        }
    });
</script>
@endpush

@push('modals')
<!-- Scanner Modal -->
<div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scannerModalLabel"><i class="fa-solid fa-barcode me-2"></i>Scan Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="position-relative bg-dark rounded overflow-hidden">
                    <video id="video" class="w-100" style="max-height: 300px; object-fit: cover;"></video>
                    <div class="scanner-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                        <div style="width: 70%; height: 50%; border: 2px solid rgba(255,255,255,0.8); box-shadow: 0 0 0 9999px rgba(0,0,0,0.5); border-radius: 8px;"></div>
                        <div style="position: absolute; top: 50%; left: 15%; right: 15%; height: 2px; background: rgba(255, 0, 0, 0.8); box-shadow: 0 0 4px red;"></div>
                    </div>
                </div>
                <div class="mt-3">
                    <select id="sourceSelect" class="form-select mb-2" style="display: none;"></select>
                    <p class="text-muted small mb-0" id="scannerStatus">Initializing camera...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endpush

@endsection
