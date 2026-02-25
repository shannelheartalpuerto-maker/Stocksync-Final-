@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin-dashboard-design.css') }}?v={{ time() }}" rel="stylesheet">

<div class="container-fluid px-4 admin-dashboard-container animate-fade-up">
    <!-- Header -->
    <div class="dashboard-header">
        <h2 class="dashboard-title"><i class="fa-solid fa-file-invoice-dollar me-3 text-primary"></i>Transactions</h2>
        <p class="dashboard-subtitle">View and manage sales history.</p>
    </div>

    <!-- Filter Section -->
    <div class="content-card mb-4">
        <div class="card-body-custom">
            <form action="{{ route('admin.transactions.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="ID or User Name" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-1">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fa-solid fa-filter"></i></button>
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header-custom">
            <h5 class="card-title-custom"><i class="fa-solid fa-receipt me-2 text-primary"></i>All Transactions</h5>
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Transaction ID</th>
                            <th>Date</th>
                            <th>Processed By</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td class="fw-bold ps-4">#{{ $transaction->id }}</td>
                            <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fa-solid fa-user me-1"></i> {{ $transaction->user->name }}
                                </span>
                            </td>
                            <td class="fw-bold text-success">₱{{ number_format($transaction->total_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fa-solid fa-eye"></i> Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fa-solid fa-receipt fa-2x mb-3 d-block opacity-50"></i>
                                No transactions found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer-custom">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
