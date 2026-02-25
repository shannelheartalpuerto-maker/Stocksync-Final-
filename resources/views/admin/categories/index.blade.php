@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin-dashboard-design.css') }}?v={{ time() }}" rel="stylesheet">
@push('styles')
<style>
    /* Force Z-Index Hierarchy to prevent black shading */
    .modal-backdrop {
        z-index: 10040 !important;
    }
    .modal {
        z-index: 10050 !important;
    }
</style>
@endpush
<div class="container-fluid px-4 admin-dashboard-container animate-fade-up">
    <!-- Header -->
    <div class="dashboard-header">
        <h2 class="dashboard-title"><i class="fa-solid fa-tags me-3 text-primary"></i>Classifications Management</h2>
        <p class="dashboard-subtitle">Organize your products into categories and brands.</p>
    </div>

    <div class="content-card mb-4">
        <div class="card-header-custom">
            <h5 class="card-title-custom mb-0"><i class="fa-solid fa-layer-group me-2"></i>Manage Classifications</h5>
        </div>

        <div class="card-body-custom">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <ul class="nav nav-tabs mb-4" id="classificationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button" role="tab" aria-controls="categories" aria-selected="true">
                        <i class="fa-solid fa-folder me-2"></i>Categories
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="brands-tab" data-bs-toggle="tab" data-bs-target="#brands" type="button" role="tab" aria-controls="brands" aria-selected="false">
                        <i class="fa-solid fa-copyright me-2"></i>Brands
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="classificationTabsContent">
                <!-- Categories Tab -->
                <div class="tab-pane fade show active" id="categories" role="tabpanel" aria-labelledby="categories-tab">
                    <div class="row g-4">
                        <!-- Add Category Section -->
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <h5 class="mb-3 fw-bold text-dark"><i class="fa-solid fa-folder-plus me-2 text-success"></i>Add New Category</h5>
                                <form method="POST" action="{{ route('admin.categories.store') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-bold small text-muted text-uppercase">Category Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-tag"></i></span>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter category name">
                                        </div>
                                        @error('name')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fa-solid fa-plus me-2"></i>Create Category
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Category List Section -->
                        <div class="col-md-8">
                            <h5 class="mb-3 fw-bold text-dark"><i class="fa-solid fa-list me-2 text-primary"></i>Category List</h5>
                            <div class="table-responsive" id="categoriesTableWrap">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">Name</th>
                                            <th>Products Count</th>
                                            <th class="ps-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="categoriesTableBody">
                                        @include('admin.categories.partials.categories_rows', ['categories' => $categories])

                                        @if($categories->isEmpty())
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-folder-open fa-2x mb-3 opacity-50"></i>
                                                <p class="mb-0">No categories found.</p>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Brands Tab -->
                <div class="tab-pane fade" id="brands" role="tabpanel" aria-labelledby="brands-tab">
                    <div class="row g-4">
                        <!-- Add Brand Section -->
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <h5 class="mb-3 fw-bold text-dark"><i class="fa-solid fa-plus-circle me-2 text-success"></i>Add New Brand</h5>
                                <form method="POST" action="{{ route('admin.brands.store') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="brand_name" class="form-label fw-bold small text-muted text-uppercase">Brand Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-copyright"></i></span>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="brand_name" name="name" required placeholder="Enter brand name">
                                        </div>
                                        @error('name')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fa-solid fa-plus me-2"></i>Create Brand
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Brand List Section -->
                        <div class="col-md-8">
                            <h5 class="mb-3 fw-bold text-dark"><i class="fa-solid fa-list me-2 text-primary"></i>Brand List</h5>
                            <div class="table-responsive" id="brandsTableWrap">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">Name</th>
                                            <th>Products Count</th>
                                            <th class="ps-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="brandsTableBody">
                                        @include('admin.categories.partials.brands_rows', ['brands' => $brands])

                                        @if($brands->isEmpty())
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-copyright fa-2x mb-3 opacity-50"></i>
                                                <p class="mb-0">No brands found.</p>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div id="ajaxCategoriesModals"></div>
        <div id="ajaxBrandsModals"></div>
    </div>
</div>
@endsection

@push('modals')
<!-- Categories Modals -->
@foreach($categories as $category)
    <!-- Edit Modal -->
    <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="editCategoryModalLabel{{ $category->id }}">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Edit Category
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="name{{ $category->id }}" class="form-label fw-bold">Category Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-tag text-muted"></i></span>
                                <input type="text" class="form-control" id="name{{ $category->id }}" name="name" value="{{ $category->name }}" required placeholder="Enter category name">
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
    <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-trash-can me-2"></i>Delete Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3 text-danger">
                        <i class="fa-solid fa-triangle-exclamation fa-4x opacity-75"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Are you sure?</h5>
                    <p class="text-muted mb-0">
                        Do you really want to delete <strong>{{ $category->name }}</strong>?<br>
                        <span class="text-danger fw-bold small">Warning: All products in this category will be affected.</span>
                    </p>
                </div>
                <div class="modal-footer bg-light border-top-0 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
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

<!-- Brands Modals -->
@foreach($brands as $brand)
    <!-- Edit Modal -->
    <div class="modal fade" id="editBrandModal{{ $brand->id }}" tabindex="-1" aria-labelledby="editBrandModalLabel{{ $brand->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="editBrandModalLabel{{ $brand->id }}">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Edit Brand
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="brand_name{{ $brand->id }}" class="form-label fw-bold">Brand Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-copyright text-muted"></i></span>
                                <input type="text" class="form-control" id="brand_name{{ $brand->id }}" name="name" value="{{ $brand->name }}" required placeholder="Enter brand name">
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
    <div class="modal fade" id="deleteBrandModal{{ $brand->id }}" tabindex="-1" aria-hidden="true">
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
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoriesForm = document.querySelector('form[action="{{ route('admin.categories.store') }}"]');
    const brandsForm = document.querySelector('form[action="{{ route('admin.brands.store') }}"]');
    const categoriesWrap = document.getElementById('categoriesTableWrap');
    const brandsWrap = document.getElementById('brandsTableWrap');
    const categoriesBody = document.getElementById('categoriesTableBody');
    const brandsBody = document.getElementById('brandsTableBody');
    const ajaxCategoriesModals = document.getElementById('ajaxCategoriesModals');
    const ajaxBrandsModals = document.getElementById('ajaxBrandsModals');
    const animateOnce = (el) => { if(!el) return; el.classList.remove('animate-fade-up'); void el.offsetWidth; el.classList.add('animate-fade-up'); setTimeout(()=>el.classList.remove('animate-fade-up'), 600); };
    const setLoading = (wrap, btn, on) => { if(wrap) wrap.classList.toggle('is-loading', !!on); if(btn) btn.classList.toggle('loading', !!on); };

    async function refreshLists() {
        const resp = await fetch(`{{ route('admin.categories.index') }}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await resp.json();
        if (categoriesBody) categoriesBody.innerHTML = data.categories_rows_html;
        if (brandsBody) brandsBody.innerHTML = data.brands_rows_html;
        if (ajaxCategoriesModals) ajaxCategoriesModals.innerHTML = data.categories_modals_html;
        if (ajaxBrandsModals) ajaxBrandsModals.innerHTML = data.brands_modals_html;
        animateOnce(categoriesBody);
        animateOnce(brandsBody);
    }

    function handleAjaxSubmit(form, wrap) {
        if (!form) return;
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            setLoading(wrap, btn, true);
            try {
                const resp = await fetch(form.action, {
                    method: form.method || 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form)
                });
                // If the server returns JSON with rows, apply them; otherwise fallback to refreshLists
                if (resp.headers.get('Content-Type')?.includes('application/json')) {
                    const data = await resp.json();
                    if (data.categories_rows_html) {
                        if (categoriesBody) categoriesBody.innerHTML = data.categories_rows_html;
                        if (ajaxCategoriesModals) ajaxCategoriesModals.innerHTML = data.categories_modals_html;
                        animateOnce(categoriesBody);
                    }
                    if (data.brands_rows_html) {
                        if (brandsBody) brandsBody.innerHTML = data.brands_rows_html;
                        if (ajaxBrandsModals) ajaxBrandsModals.innerHTML = data.brands_modals_html;
                        animateOnce(brandsBody);
                    }
                } else {
                    await refreshLists();
                }
                form.reset && form.reset();
            } finally {
                setLoading(wrap, btn, false);
            }
        });
    }
    handleAjaxSubmit(categoriesForm, categoriesWrap);
    handleAjaxSubmit(brandsForm, brandsWrap);

    document.addEventListener('submit', async function(e) {
        const form = e.target;
        const isCatUpdate = form.action.includes('/admin/categories/') && form.querySelector('input[name="_method"][value="PUT"]');
        const isCatDelete = form.action.includes('/admin/categories/') && form.querySelector('input[name="_method"][value="DELETE"]');
        const isBrandUpdate = form.action.includes('/admin/brands/') && form.querySelector('input[name="_method"][value="PUT"]');
        const isBrandDelete = form.action.includes('/admin/brands/') && form.querySelector('input[name="_method"][value="DELETE"]');
        if (isCatUpdate || isCatDelete || isBrandUpdate || isBrandDelete) {
            e.preventDefault();
            const wrap = (isCatUpdate || isCatDelete) ? categoriesWrap : brandsWrap;
            const modalEl = form.closest('.modal');
            const submitBtn = form.querySelector('button[type="submit"]');
            setLoading(wrap, submitBtn, true);
            try {
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form)
                });
                if (resp.headers.get('Content-Type')?.includes('application/json')) {
                    const data = await resp.json();
                    if (data.categories_rows_html) {
                        if (categoriesBody) categoriesBody.innerHTML = data.categories_rows_html;
                        if (ajaxCategoriesModals) ajaxCategoriesModals.innerHTML = data.categories_modals_html;
                        animateOnce(categoriesBody);
                    }
                    if (data.brands_rows_html) {
                        if (brandsBody) brandsBody.innerHTML = data.brands_rows_html;
                        if (ajaxBrandsModals) ajaxBrandsModals.innerHTML = data.brands_modals_html;
                        animateOnce(brandsBody);
                    }
                } else {
                    await refreshLists();
                }
            } finally {
                setLoading(wrap, submitBtn, false);
                if (modalEl) {
                    const instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    instance.hide();
                }
            }
        }
    });
});
</script>
@endpush
