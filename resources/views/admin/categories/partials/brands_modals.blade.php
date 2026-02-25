@foreach($brands as $brand)
    <!-- Edit Modal -->
    <div class="modal fade" id="ajax-editBrandModal{{ $brand->id }}" tabindex="-1" aria-labelledby="ajax-editBrandModalLabel{{ $brand->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="ajax-editBrandModalLabel{{ $brand->id }}">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Edit Brand
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Brand Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-copyright text-muted"></i></span>
                                <input type="text" class="form-control" name="name" value="{{ $brand->name }}" required placeholder="Enter brand name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fa-solid fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="ajax-deleteBrandModal{{ $brand->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-trash-can me-2"></i>Delete Brand</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3 text-danger">
                        <i class="fa-solid fa-triangle-exclamation fa-4x opacity-75"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Are you sure?</h5>
                    <p class="text-muted mb-0">
                        Do you really want to delete <strong>{{ $brand->name }}</strong>?<br>
                        <span class="text-danger fw-bold small">Warning: Products linked to this brand will be unlinked.</span>
                    </p>
                </div>
                <div class="modal-footer bg-light border-top-0 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 fw-bold">
                            <i class="fa-solid fa-trash me-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
