@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin-dashboard-design.css') }}?v={{ time() }}" rel="stylesheet">
<div class="container-fluid px-4 admin-dashboard-container animate-fade-up">
    <!-- Header -->
    <div class="dashboard-header">
        <h2 class="dashboard-title"><i class="fa-solid fa-user-shield me-3 text-primary"></i>Admin Management</h2>
        <p class="dashboard-subtitle">Manage fellow admin accounts and access levels.</p>
    </div>

    <div class="content-card mb-4">
        <div class="card-header-custom">
            <h5 class="card-title-custom mb-0"><i class="fa-solid fa-users-gear me-2"></i>Manage Admins</h5>
        </div>

        <div class="card-body-custom">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row g-4">
                <!-- Add Admin Section -->
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <h5 class="mb-3 fw-bold text-dark"><i class="fa-solid fa-user-plus me-2 text-success"></i>Add New Admin</h5>
                        <form method="POST" action="{{ route('admin.admins.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold small text-muted text-uppercase">Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Full Name">
                                </div>
                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold small text-muted text-uppercase">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="email@example.com">
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold small text-muted text-uppercase">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="********">
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password-confirm" class="form-label fw-bold small text-muted text-uppercase">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password-confirm" name="password_confirmation" required placeholder="********">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-plus me-2"></i>Create Admin Account
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Admin List Section -->
                <div class="col-md-8">
                    <h5 class="mb-3 fw-bold text-dark"><i class="fa-solid fa-list me-2 text-primary"></i>Admin List</h5>
                    <div class="table-responsive" id="adminsTableWrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th class="ps-3" style="min-width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="adminsTableBody">
                                @include('admin.admins.partials.rows', ['admins' => $admins])

                                @if($admins->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-users-slash fa-2x mb-3 opacity-50"></i>
                                        <p class="mb-0">No other admin accounts found.</p>
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

<!-- Modals Section -->
<div id="ajaxAdminsModals">
    @include('admin.admins.partials.modals', ['admins' => $admins])
</div>

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const createForm = document.querySelector('form[action="{{ route('admin.admins.store') }}"]');
    const tableWrap = document.getElementById('adminsTableWrap');
    const tbody = document.getElementById('adminsTableBody');
    const modalsMount = document.getElementById('ajaxAdminsModals');
    const animateOnce = (el) => { if(!el) return; el.classList.remove('animate-fade-up'); void el.offsetWidth; el.classList.add('animate-fade-up'); setTimeout(()=>el.classList.remove('animate-fade-up'), 600); };
    const setLoading = (wrap, btn, on) => { if(wrap) wrap.classList.toggle('is-loading', !!on); if(btn) btn.classList.toggle('loading', !!on); };

    async function refreshList() {
        const resp = await fetch(`{{ route('admin.admins.index') }}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await resp.json();
        if (tbody && data.rows_html) tbody.innerHTML = data.rows_html;
        if (modalsMount && data.modals_html) modalsMount.innerHTML = data.modals_html;
        animateOnce(tbody);
    }

    function handleAjaxSubmit(form, wrap) {
        if (!form) return;
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            setLoading(wrap, btn, true);
            try {
                const resp = await fetch(form.action, { method: form.method || 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(form) });
                if (resp.headers.get('Content-Type')?.includes('application/json')) {
                    const data = await resp.json();
                    if (data.rows_html) tbody.innerHTML = data.rows_html;
                    if (data.modals_html) modalsMount.innerHTML = data.modals_html;
                    animateOnce(tbody);
                } else {
                    await refreshList();
                }
                form.reset && form.reset();
            } finally {
                setLoading(wrap, btn, false);
            }
        });
    }
    handleAjaxSubmit(createForm, tableWrap);

    document.addEventListener('submit', async function(e) {
        const form = e.target;
        const isUpdate = form.action.includes('/admin/admins/') && form.querySelector('input[name="_method"][value="PUT"]');
        const isDelete = form.action.includes('/admin/admins/') && form.querySelector('input[name="_method"][value="DELETE"]');
        if (isUpdate || isDelete) {
            e.preventDefault();
            const modalEl = form.closest('.modal');
            const btn = form.querySelector('button[type="submit"]');
            setLoading(tableWrap, btn, true);
            try {
                const resp = await fetch(form.action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(form) });
                if (resp.headers.get('Content-Type')?.includes('application/json')) {
                    const data = await resp.json();
                    if (data.rows_html) tbody.innerHTML = data.rows_html;
                    if (data.modals_html) modalsMount.innerHTML = data.modals_html;
                } else {
                    await refreshList();
                }
                animateOnce(tbody);
            } finally {
                setLoading(tableWrap, btn, false);
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
