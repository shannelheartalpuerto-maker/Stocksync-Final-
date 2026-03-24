@extends('layouts.app')

@section('content')
<link href="{{ asset('css/staff-design.css') }}?v={{ time() }}" rel="stylesheet">
<style>
.inv-topbar {
    position: relative;
    overflow: hidden;
    border-radius: 18px;
    padding: 1.05rem 1.3rem;
    background: linear-gradient(130deg, #0f766e 0%, #0ea5e9 55%, #2563eb 100%);
    border: 1px solid rgba(255, 255, 255, 0.22);
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
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,.18);
    color: #fff;
    font-size: 1rem;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
}
.inv-title-text {
    font-size: 1.85rem;
    font-weight: 750;
    letter-spacing: -0.35px;
    color: #fff;
    line-height: 1.05;
    margin: 0;
}
.inv-header-actions {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.inv-head-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.5rem 0.95rem;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,.38);
    background: rgba(255,255,255,.16);
    color: #fff;
    font-size: 0.9rem;
    font-weight: 650;
    white-space: nowrap;
}
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
.sales-filter-shell {
    background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
}
.sales-table-shell {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
    background: #fff;
}
.sales-details-btn {
    border-radius: 10px;
    border: 1px solid #dbe2ea;
    background: #fff;
    color: #334155;
    font-weight: 600;
    transition: all .18s ease;
}
.sales-details-btn:hover {
    border-color: #bfdbfe;
    background: #f8fbff;
    color: #1d4ed8;
}
</style>

<div class="staff-container animate-fade-up">
    <div class="inv-topbar mb-4">
        <div class="inv-topbar-inner">
            <div class="inv-title-wrap">
                <span class="inv-title-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                <h5 class="inv-title-text">Sales Transactions</h5>
            </div>
            <div class="inv-header-actions">
                <span class="inv-head-pill"><i class="fa-solid fa-receipt"></i>{{ method_exists($transactions, 'total') ? number_format($transactions->total()) : number_format($transactions->count()) }} Entries</span>
                <span class="inv-head-pill"><i class="fa-solid fa-money-bill-trend-up"></i>Revenue Tracking</span>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-body-custom">
            <!-- Filter Section -->
            <div class="sales-filter-shell mb-4">
            <div class="filter-section mb-0">
                <form action="{{ route('admin.transactions.index') }}" method="GET" id="transactionsFilterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="search-input-group">
                                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                <input type="text" name="search" class="form-control search-control" placeholder="Search by TRX ID or User..." value="{{ request('search') }}">
                                @if(request('search'))
                                    <a href="{{ route('admin.transactions.index', request()->except('search')) }}" class="btn-clear-search">
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group search-input-group border">
                                <span class="input-group-text bg-transparent border-0 small text-muted fw-bold">FROM</span>
                                <input type="date" name="start_date" class="form-control border-0 bg-transparent" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group search-input-group border">
                                <span class="input-group-text bg-transparent border-0 small text-muted fw-bold">TO</span>
                                <input type="date" name="end_date" class="form-control border-0 bg-transparent" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-indigo flex-grow-1">Filter</button>
                                <a href="{{ route('admin.transactions.index') }}" class="btn btn-light" title="Reset Filters">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            </div>

            <div class="sales-table-shell">
            <div class="table-responsive" id="transactionsTableWrap">
                <table class="table align-middle inv-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Transaction ID</th>
                            <th>Date & Time</th>
                            <th>Processed By</th>
                            <th>Total Amount</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-indigo border fw-bold px-3 py-2">#{{ $transaction->id }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $transaction->created_at->format('M d, Y') }}</div>
                                <div class="text-muted small">{{ $transaction->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="fa-solid fa-user small text-muted"></i>
                                    </div>
                                    <span class="text-dark fw-medium">{{ $transaction->user->name }}</span>
                                </div>
                            </td>
                            <td><span class="fw-bold text-success fs-6">₱{{ number_format($transaction->total_amount, 2) }}</span></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="btn btn-sm px-3 sales-details-btn">
                                    <i class="fa-solid fa-eye me-1 text-primary"></i>Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fa-solid fa-receipt fa-3x mb-3 text-muted opacity-25"></i>
                                    <h6 class="text-muted">No transactions found matching your criteria</h6>
                                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-link text-decoration-none">Clear all filters</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </div>
        
        @if($transactions->hasPages())
        <div class="card-footer-custom bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} entries
                </div>
                <div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('transactionsFilterForm');
        
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

        // Auto-submit on input (except dates which might be confusing)
        const searchInput = form.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function() {
                form.submit();
            }, 600));
        }

        // Auto-submit on date change
        form.querySelectorAll('input[type="date"]').forEach(dateInput => {
            dateInput.addEventListener('change', function() {
                form.submit();
            });
        });
    });
</script>
@endsection
