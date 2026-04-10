@foreach($products as $product)

    {{-- ── Stock In Modal ── --}}
    <div class="modal fade" id="ajax-stockInModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
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
                                <h5 class="modal-title fw-bold mb-0">Stock In</h5>
                                <p class="text-muted small mb-0">{{ $product->name }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
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
                        <button type="submit" class="btn btn-success px-4 fw-semibold">Update Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Report Damage Modal ── --}}
    <div class="modal fade" id="ajax-damagedModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
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
                                <h5 class="modal-title fw-bold mb-0">Report Damage</h5>
                                <p class="text-muted small mb-0">{{ $product->name }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Quantity Damaged</label>
                            <div class="input-group search-input-group border">
                                <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-hashtag text-warning"></i></span>
                                <input type="number" class="form-control border-0 bg-transparent" name="quantity" min="1" max="{{ $product->quantity }}" required>
                            </div>
                            <small class="text-muted">Available stock: {{ $product->quantity }}</small>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark">Reason / Notes</label>
                            <textarea class="form-control bg-light border-0" name="notes" rows="3" placeholder="Describe the damage..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4 fw-semibold text-white">Report Damage</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Delete Confirmation Modal ── --}}
    <div class="modal fade" id="ajax-deleteModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-4 text-center">
                    <div class="bg-danger bg-opacity-10 d-inline-flex p-3 rounded-circle mb-3">
                        <i class="fa-solid fa-trash-can text-danger fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Delete Product?</h5>
                    <p class="text-muted small mb-0">This action cannot be undone. Are you sure you want to delete <strong>{{ $product->name }}</strong>?</p>
                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <button type="button" class="btn btn-light px-4 flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 px-4 fw-semibold">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Edit Product Modal ── --}}
    <div class="modal fade" id="ajax-editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
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
                            <div class="col-md-5">
                                <div class="bg-light rounded-4 p-3 text-center h-100">
                                    <label class="form-label fw-bold text-dark mb-2">Product Image</label>
                                    <div class="mb-2" style="height:140px;overflow:hidden;position:relative;border-radius:.75rem;background:white;border:2px dashed #e2e8f0;">
                                        <img src="{{ $product->image ? asset('storage/' . $product->image) : '#' }}" alt="" style="display:{{ $product->image ? 'block' : 'none' }};width:100%;height:100%;object-fit:cover;">
                                        <div class="text-muted d-flex flex-column align-items-center justify-content-center h-100" style="display:{{ $product->image ? 'none' : 'flex' }} !important;">
                                            <i class="fa-solid fa-cloud-arrow-up fa-2x mb-1 opacity-25"></i>
                                            <p class="small mb-0">Click to upload</p>
                                        </div>
                                        <input type="file" name="image" accept="image/*" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;">
                                    </div>
                                    <div class="text-start mt-2">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Category</label>
                                            <select class="form-select border-0 bg-white shadow-sm" name="category_id">
                                                <option value="" {{ is_null($product->category_id) ? 'selected' : '' }}>N/A</option>
                                                @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Brand</label>
                                            <select class="form-select border-0 bg-white shadow-sm" name="brand_id">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Product Name</label>
                                    <input type="text" class="form-control border-0 bg-light" name="name" value="{{ $product->name }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-bold">SKU / Barcode</label>
                                    <div class="input-group search-input-group border">
                                        <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-barcode text-muted"></i></span>
                                        <input type="text" class="form-control border-0 bg-transparent" name="code" value="{{ $product->code }}" required>
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
                                <div class="bg-white rounded-4 p-3">
                                    <h6 class="fw-bold mb-2 text-indigo"><i class="fa-solid fa-sliders me-2"></i>Inventory Thresholds</h6>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <label class="form-label small fw-bold text-muted mb-1">Low Stock</label>
                                            <input type="number" class="form-control border-0 shadow-sm" name="low_stock_threshold" value="{{ $product->low_stock_threshold ?? 10 }}" min="0" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small fw-bold text-muted mb-1">Good Stock</label>
                                            <input type="number" class="form-control border-0 shadow-sm" name="good_stock_threshold" value="{{ $product->good_stock_threshold ?? 50 }}" min="0" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small fw-bold text-muted mb-1">Overstock</label>
                                            <input type="number" class="form-control border-0 shadow-sm" name="overstock_threshold" value="{{ $product->overstock_threshold ?? 100 }}" min="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endforeach
