@foreach($products as $product)
    <!-- Stock In Modal -->
    <div class="modal fade" id="ajax-stockInModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
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
    <div class="modal fade" id="ajax-damagedModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
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
    <div class="modal fade" id="ajax-deleteModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
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
    <div class="modal fade" id="ajax-editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
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
                            <div class="col-md-5">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body text-center p-3">
                                        <label class="form-label fw-bold text-dark mb-3">Product Image</label>
                                        <div class="image-preview-container mb-3 d-flex align-items-center justify-content-center bg-white border rounded shadow-sm" style="height: 200px; overflow: hidden; position: relative;">
                                            <img src="{{ $product->image ? asset('storage/' . $product->image) : '#' }}" alt="Preview" style="display: {{ $product->image ? 'block' : 'none' }}; width: 100%; height: 100%; object-fit: cover;">
                                            <div class="text-muted" style="display: {{ $product->image ? 'none' : 'block' }};">
                                                <i class="fa-solid fa-cloud-arrow-up fa-3x mb-2 opacity-50"></i>
                                                <p class="small mb-0">Click to change</p>
                                            </div>
                                            <input type="file" class="form-control" name="image" accept="image/*" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
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
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Product Name</label>
                                    <input type="text" class="form-control form-control-lg" name="name" value="{{ $product->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Code/SKU (Barcode)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-barcode text-muted"></i></span>
                                        <input type="text" class="form-control" name="code" value="{{ $product->code }}" required>
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
                                                <input type="number" class="form-control form-control-sm" name="low_stock_threshold" value="{{ $product->low_stock_threshold ?? 10 }}" min="0" required>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small fw-bold text-muted">Good Stock</label>
                                                <input type="number" class="form-control form-control-sm" name="good_stock_threshold" value="{{ $product->good_stock_threshold ?? 50 }}" min="0" required>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small fw-bold text-muted">Overstock</label>
                                                <input type="number" class="form-control form-control-sm" name="overstock_threshold" value="{{ $product->overstock_threshold ?? 100 }}" min="0" required>
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
