@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin-dashboard-design.css') }}?v={{ time() }}" rel="stylesheet">

@push('styles')
<style>
    /* Force Z-Index Hierarchy to prevent black shading */
    /* Remove static high z-index to let JS handle it dynamically */
</style>
@endpush
<div class="container-fluid px-4 admin-dashboard-container animate-fade-up">
    <!-- Header -->
    <div class="dashboard-header">
        <h2 class="dashboard-title"><i class="fa-solid fa-box-open me-3 text-primary"></i>Product Management</h2>
        <p class="dashboard-subtitle">Manage your inventory, prices, and stock levels.</p>
    </div>

    <div class="content-card mb-4">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h5 class="card-title-custom mb-0"><i class="fa-solid fa-boxes-stacked me-2"></i>Product List</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fa-solid fa-plus me-2"></i>Add New Product
            </button>
        </div>

        <div class="card-body-custom">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filter Form -->
            <form action="{{ route('admin.products.index') }}" method="GET" class="mb-4" id="productsFilterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search..." value="{{ request('search') }}">
                            <button class="btn btn-outline-primary" type="submit" id="productsSearchBtn">Go</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="category" class="form-select">
                            <option value="all">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="brand" class="form-select">
                            <option value="all">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="all">All Status</option>
                            <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                            <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="good" {{ request('status') == 'good' ? 'selected' : '' }}>Good Stock</option>
                            <option value="over" {{ request('status') == 'over' ? 'selected' : '' }}>Overstock</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Image</th>
                        <th>Code/SKU</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="ps-3">Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
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
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                                            <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#stockInModal{{ $product->id }}">
                                            <i class="fa-solid fa-arrow-down me-2 text-success"></i>Stock In
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#damagedModal{{ $product->id }}">
                                            <i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>Report Damaged
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}">
                                            <i class="fa-solid fa-trash me-2"></i>Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    @endforeach

                    @if($products->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fa-2x mb-3 opacity-50"></i>
                            <p class="mb-0">No products found.</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="card-footer-custom" id="productsPagination">
            @if($products->hasPages())
                {{ $products->links() }}
            @endif
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
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('admin.products.stock_in', $product->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-arrow-down me-2"></i>Stock In: {{ $product->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Quantity to Add</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-plus text-success"></i></span>
                                <input type="number" class="form-control" name="quantity" min="1" placeholder="Enter quantity" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Optional notes (e.g. Supplier invoice #)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold"><i class="fa-solid fa-check me-2"></i>Add Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Damaged Report Modal -->
    <div class="modal fade" id="damagedModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('admin.products.damaged', $product->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Report Damaged Product</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Quantity Damaged</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-hashtag text-danger"></i></span>
                                <input type="number" class="form-control" name="quantity" min="1" max="{{ $product->quantity }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Describe the damage..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Report Damaged</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-trash-can me-2"></i>Delete Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3 text-danger">
                        <i class="fa-solid fa-circle-exclamation fa-4x opacity-75"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Are you sure?</h5>
                    <p class="text-muted mb-0">Do you really want to delete <strong>{{ $product->name }}</strong>? This process cannot be undone.</p>
                </div>
                <div class="modal-footer bg-light border-top-0 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 fw-bold"><i class="fa-solid fa-trash me-2"></i>Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Product: {{ $product->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <!-- Left Column -->
                            <div class="col-md-5">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body text-center p-3">
                                        <label class="form-label fw-bold text-dark mb-3">Product Image</label>
                                        
                                        <div class="image-preview-container mb-3 d-flex align-items-center justify-content-center bg-white border rounded shadow-sm" style="height: 200px; overflow: hidden; position: relative;">
                                            <img id="editImagePreview{{ $product->id }}" src="{{ $product->image ? asset('storage/' . $product->image) : '#' }}" alt="Preview" style="display: {{ $product->image ? 'block' : 'none' }}; width: 100%; height: 100%; object-fit: cover;">
                                            <div id="editImagePlaceholder{{ $product->id }}" class="text-muted" style="display: {{ $product->image ? 'none' : 'block' }};">
                                                <i class="fa-solid fa-cloud-arrow-up fa-3x mb-2 opacity-50"></i>
                                                <p class="small mb-0">Click to change</p>
                                            </div>
                                            <input type="file" class="form-control" name="image" accept="image/*" id="editImageInput{{ $product->id }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" onchange="previewEditImage(this, '{{ $product->id }}')">
                                        </div>
                                        <small class="text-muted d-block">Click image to upload new one</small>

                                        <div class="mt-4 text-start">
                                            <label class="form-label fw-bold">Category</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-layer-group text-muted"></i></span>
                                                <select class="form-select border-start-0 ps-0" name="category_id" required>
                                                    @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mt-3 text-start">
                                            <label class="form-label fw-bold">Brand</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-copyright text-muted"></i></span>
                                                <select class="form-select border-start-0 ps-0" name="brand_id">
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
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Product Name</label>
                                    <input type="text" class="form-control form-control-lg" name="name" value="{{ $product->name }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Code/SKU (Barcode)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-barcode text-muted"></i></span>
                                        <input type="text" class="form-control" name="code" id="editProductCode{{ $product->id }}" value="{{ $product->code }}" required>
                                        <button class="btn btn-outline-primary btn-choose-barcode-file-edit" type="button" data-target-input="editProductCode{{ $product->id }}">
                                            <i class="fa-solid fa-camera"></i> Scan
                                        </button>
                                        <input type="file" class="hidden-barcode-file-edit" accept="image/*" style="display: none;">
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">₱</span>
                                            <input type="number" step="0.01" class="form-control" name="price" value="{{ $product->price }}" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Quantity</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="fa-solid fa-cubes text-muted"></i></span>
                                            <input type="number" class="form-control" name="quantity" value="{{ $product->quantity }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-sliders me-2"></i>Inventory Thresholds</h6>
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <label class="form-label small fw-bold text-muted">Low Stock</label>
                                                <input type="number" class="form-control form-control-sm threshold-input" name="low_stock_threshold" value="{{ $product->low_stock_threshold ?? 10 }}" min="0" required>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small fw-bold text-muted">Good Stock</label>
                                                <input type="number" class="form-control form-control-sm threshold-input" name="good_stock_threshold" value="{{ $product->good_stock_threshold ?? 50 }}" min="0" required>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small fw-bold text-muted">Overstock</label>
                                                <input type="number" class="form-control form-control-sm threshold-input" name="overstock_threshold" value="{{ $product->overstock_threshold ?? 100 }}" min="0" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fa-solid fa-save me-2"></i>Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="addProductForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="addProductModalLabel"><i class="fa-solid fa-box-open me-2"></i>Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Display Errors inside Modal -->
                    @if ($errors->any() && !old('_method'))
                        <div class="alert alert-danger shadow-sm rounded-3">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-4">
                        <!-- Left Column: Image & Basic Info -->
                        <div class="col-md-5">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-center p-3">
                                    <label class="form-label fw-bold text-dark mb-3">Product Image</label>
                                    
                                    <div class="image-preview-container mb-3 d-flex align-items-center justify-content-center bg-white border rounded shadow-sm" style="height: 200px; overflow: hidden; position: relative;">
                                        <img id="addImagePreview" src="#" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                        <div id="addImagePlaceholder" class="text-muted">
                                            <i class="fa-solid fa-cloud-arrow-up fa-3x mb-2 opacity-50"></i>
                                            <p class="small mb-0">Click to upload</p>
                                        </div>
                                        <input type="file" class="form-control" name="image" accept="image/*" id="addImageInput" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                                    </div>
                                    <small class="text-muted d-block">Recommended: Square image, max 2MB</small>

                                    <div class="mt-4 text-start">
                                        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-layer-group text-muted"></i></span>
                                            <select class="form-select border-start-0 ps-0" name="category_id" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3 text-start">
                                        <label class="form-label fw-bold">Brand</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-copyright text-muted"></i></span>
                                            <select class="form-select border-start-0 ps-0" name="brand_id">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Details & Inventory -->
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="name" value="{{ old('name') }}" placeholder="e.g. Ballpen" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Code/SKU (Barcode) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fa-solid fa-barcode text-muted"></i></span>
                                    <input type="text" class="form-control" name="code" id="addProductCode" value="{{ old('code') }}" placeholder="Scan or type code" required>
                                    <button class="btn btn-outline-primary" type="button" id="btn-choose-barcode-file">
                                        <i class="fa-solid fa-camera"></i> Scan
                                    </button>
                                    <input type="file" id="hidden-barcode-file" accept="image/*" style="display: none;">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-bold">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">₱</span>
                                        <input type="number" step="0.01" class="form-control" name="price" value="{{ old('price') }}" placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">Initial Stock <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-cubes text-muted"></i></span>
                                        <input type="number" class="form-control" name="quantity" value="{{ old('quantity', 0) }}" placeholder="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-sliders me-2"></i>Inventory Thresholds</h6>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <label class="form-label small fw-bold text-muted">Low Stock</label>
                                            <input type="number" class="form-control form-control-sm threshold-input" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" min="0" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small fw-bold text-muted">Good Stock</label>
                                            <input type="number" class="form-control form-control-sm threshold-input" name="good_stock_threshold" value="{{ old('good_stock_threshold', 50) }}" min="0" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small fw-bold text-muted">Overstock</label>
                                            <input type="number" class="form-control form-control-sm threshold-input" name="overstock_threshold" value="{{ old('overstock_threshold', 100) }}" min="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fa-solid fa-plus me-2"></i>Add Product</button>
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
                        // Reset manual inputs that might not be covered by reset() if value attribute is set
                        // But standard reset() should handle it.
                        // We also need to reset the "Choose File" button text if it was changed
                         if (addChooseFileBtn) {
                             addChooseFileBtn.innerHTML = '<i class="fa-solid fa-camera"></i> Scan';
                             addChooseFileBtn.disabled = false;
                         }
                         // Reset Image Preview
                         if (addImagePreview && addImagePlaceholder) {
                             addImagePreview.src = '#';
                             addImagePreview.style.display = 'none';
                             addImagePlaceholder.style.display = 'block';
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
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';
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
                         if (btn.innerHTML.includes('Adding...')) {
                             btn.innerHTML = 'Add Product'; 
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
        const card = form ? form.closest('.content-card') : null;
        const animateOnce = (el) => {
            if (!el) return;
            el.classList.remove('animate-fade-up');
            void el.offsetWidth;
            el.classList.add('animate-fade-up');
            setTimeout(() => el.classList.remove('animate-fade-up'), 600);
        };
        const setLoading = (on) => {
            if (card) card.classList.toggle('is-loading', !!on);
            if (searchBtn) searchBtn.classList.toggle('loading', !!on);
        };
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
