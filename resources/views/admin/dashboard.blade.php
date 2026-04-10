@extends('layouts.app')

@section('content')
<link href="{{ asset('css/staff-design.css') }}?v={{ time() }}" rel="stylesheet">
<style>
/* Admin Dashboard */
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
.db-tab-strip {
    background: transparent;
    border-radius: 0;
    padding: 0;
    gap: 0.65rem;
    border: none;
    border-bottom: none;
}
.db-tab-strip .nav-link {
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    color: #6b7280;
    padding: 0.65rem 1.35rem;
    border: 1px solid #dbe2ea;
    transition: all .18s ease;
    white-space: nowrap;
    background: #fff;
}
.db-tab-strip .nav-link.active {
    background: #f8fafc !important;
    color: #4f46e5 !important;
    border-color: #c7d2fe;
    box-shadow: 0 2px 8px rgba(79,70,229,.14) !important;
}
.db-tab-strip .nav-link:hover:not(.active) {
    color: #4f46e5 !important;
    background: #f8fafc !important;
    border-color: #d6ddeb;
}
</style>
<div class="container-fluid px-4 staff-container animate-fade-up">

    @if($showAdminGuide)
    <div class="modal fade" id="adminIntroModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-4 d-inline-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-rocket text-primary fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-1">Welcome to StockSync</h5>
                            <p class="text-muted small mb-0">A quick setup guide for your new admin account.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pt-2 pb-4">
                    <div class="alert alert-primary border-0 rounded-4 mb-4" style="background: linear-gradient(135deg, rgba(79,70,229,.08), rgba(14,165,233,.08));">
                        <div class="fw-bold mb-1">Important first step</div>
                        <div class="small text-secondary">Create a category before adding products so your inventory stays organized.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 h-100 bg-light">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width:44px;height:44px;">
                                    <i class="fa-solid fa-folder-tree"></i>
                                </div>
                                <h6 class="fw-bold mb-2">1. Create a Category</h6>
                                <p class="text-muted small mb-0">Go to Categories first and add sections like Paper, Snacks, or Supplies.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 h-100 bg-light">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success mb-3" style="width:44px;height:44px;">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                                <h6 class="fw-bold mb-2">2. Add Products</h6>
                                <p class="text-muted small mb-0">After categories exist, add products and assign them to the correct category.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 h-100 bg-light">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning mb-3" style="width:44px;height:44px;">
                                    <i class="fa-solid fa-chart-column"></i>
                                </div>
                                <h6 class="fw-bold mb-2">3. Review Inventory</h6>
                                <p class="text-muted small mb-0">Use the dashboard and stock tabs to monitor product levels and restock needs.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4" data-guide-href="{{ route('admin.categories.index') }}">
                        <i class="fa-solid fa-folder-tree me-2"></i>Go to Categories
                    </button>
                    <button type="button" class="btn btn-primary px-4 fw-semibold" data-guide-href="{{ route('admin.products.index') }}">
                        <i class="fa-solid fa-box-open me-2"></i>Start Adding Products
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Mobile Header (mirrors inv-topbar + action buttons, mobile only) --}}
    <div class="mobile-topbar-card" style="display:none;">
        <div class="inv-topbar-inner">
            <div class="inv-title-wrap">
                <span class="inv-title-icon"><i class="fa-solid fa-chart-line"></i></span>
                <h5 class="inv-title-text">Admin Dashboard</h5>
            </div>
            <div class="inv-header-actions">
                <span class="inv-head-pill"><i class="fa-solid fa-layer-group"></i>{{ number_format($categoryStats->count()) }} Categories</span>
                <span class="inv-head-pill"><i class="fa-solid fa-clock-rotate-left"></i>{{ number_format($recentLogs->count()) }} Recent Logs</span>
            </div>
        </div>
        <div class="mobile-topbar-actions">
            <a href="{{ route('admin.reports.restock_list') }}" target="_blank" class="mobile-topbar-btn">
                <i class="fa-solid fa-truck-ramp-box"></i> Restock
            </a>
            <a href="{{ route('admin.products.index', ['addProduct' => 1]) }}" class="mobile-topbar-btn">
                <i class="fa-solid fa-plus"></i> Quick Add
            </a>
        </div>
    </div>

    {{-- Mobile Quick Access Grid (visible only on mobile) --}}
    <div class="mobile-quick-grid" style="display:none;">
        <a href="{{ route('admin.staff.index') }}" class="quick-item">
            <div class="quick-icon"><i class="fa-solid fa-users"></i></div>
            <span class="quick-label">Staff</span>
        </a>
        <a href="{{ route('admin.admins.index') }}" class="quick-item">
            <div class="quick-icon"><i class="fa-solid fa-user-shield"></i></div>
            <span class="quick-label">Admins</span>
        </a>
    </div>

    <div class="inv-topbar mb-4">
        <div class="inv-topbar-inner">
            <div class="inv-title-wrap">
                <span class="inv-title-icon"><i class="fa-solid fa-chart-line"></i></span>
                <h5 class="inv-title-text">Admin Dashboard</h5>
            </div>
            <div class="inv-header-actions">
                <span class="inv-head-pill"><i class="fa-solid fa-layer-group"></i>{{ number_format($categoryStats->count()) }} Categories</span>
                <span class="inv-head-pill"><i class="fa-solid fa-clock-rotate-left"></i>{{ number_format($recentLogs->count()) }} Recent Logs</span>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-4">
        <ul class="nav db-tab-strip" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="product-tab" data-bs-toggle="tab" data-bs-target="#product" type="button" role="tab">
                    <i class="fa-solid fa-box-open me-2"></i>Product Data
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
                    <i class="fa-solid fa-chart-line me-2"></i>Sales
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="forecast-tab" data-bs-toggle="tab" data-bs-target="#forecast" type="button" role="tab">
                    <i class="fa-solid fa-bullseye me-2"></i>Forecast
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="dashboardTabsContent">
        <!-- Product Data Tab -->
        <div class="tab-pane fade show active" id="product" role="tabpanel">
            <!-- Product Stats -->
            <div class="row g-3 mb-4">
                 <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Total Products</span>
                            <div class="stat-icon icon-primary"><i class="fa-solid fa-box-open"></i></div>
                        </div>
                        <h3 class="stat-value">{{ number_format($totalProducts) }}</h3>
                    </div>
                 </div>
                 <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Total Stock</span>
                            <div class="stat-icon icon-info"><i class="fa-solid fa-layer-group"></i></div>
                        </div>
                        <h3 class="stat-value">{{ number_format($totalStockCount) }}</h3>
                    </div>
                 </div>
                 <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label text-success">Inventory Value</span>
                            <div class="stat-icon icon-success"><i class="fa-solid fa-money-bill-trend-up"></i></div>
                        </div>
                        <h3 class="stat-value text-success">₱{{ number_format($totalStockValue, 2) }}</h3>
                    </div>
                 </div>
                 <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label text-warning">Top Mover</span>
                            <div class="stat-icon icon-warning"><i class="fa-solid fa-fire"></i></div>
                        </div>
                        <div class="d-flex flex-column">
                            <h4 class="fw-bold mb-0 text-truncate text-dark" style="font-size: 1.1rem;">{{ $topMovingProduct->name ?? 'N/A' }}</h4>
                            <small class="text-muted fw-bold">{{ number_format($topMovingProduct->total_sold ?? 0) }} sold</small>
                        </div>
                    </div>
                 </div>
            </div>
            
            <div class="row g-4">
                <!-- Recent Stock Activity -->
                <div class="col-lg-8">
                     <div class="content-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="card-title-custom mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Recent Stock Activity</h5>
                            <a href="{{ route('admin.stock_logs.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                        </div>
                        <div class="card-body-custom p-0">
                            <div class="list-group list-group-flush">
                                @forelse($recentLogs as $log)
                                    <div class="list-group-item px-4 py-3 border-bottom border-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 rounded-circle p-2 d-flex align-items-center justify-content-center {{ $log->type == 'stock_in' ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}" style="width: 40px; height: 40px;">
                                                    <i class="fa-solid {{ $log->type == 'stock_in' ? 'fa-arrow-down' : 'fa-arrow-up' }} small"></i>
                                                </div>
                                                <div>
                                                    <div class="mb-0 fw-bold text-dark">{{ $log->product->name ?? 'Unknown' }}</div>
                                                    <div class="text-muted small fw-500">{{ $log->created_at->diffForHumans() }} • {{ $log->user->name ?? 'System' }}</div>
                                                </div>
                                            </div>
                                            <span class="badge {{ $log->type == 'stock_in' ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} rounded-pill px-3 py-2 fw-bold">
                                                {{ $log->type == 'stock_in' ? '+' : '-' }}{{ $log->quantity }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-5 text-center text-muted">
                                        <i class="fa-regular fa-clipboard mb-3 fa-2x opacity-25"></i>
                                        <p class="mb-0 fw-bold">No recent activity found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                     </div>
                </div>
                
                <!-- Category Distribution -->
                <div class="col-lg-4">
                    <div class="content-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="card-title-custom mb-0"><i class="fa-solid fa-folder-tree me-2 text-primary"></i>Categories</h5>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-light rounded-circle shadow-sm"><i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                        <div class="card-body-custom overflow-auto" style="max-height: 450px;">
                            @forelse($categoryStats as $category)
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-bold text-dark small text-uppercase letter-spacing-1">{{ $category->name }}</span>
                                        <span class="badge bg-light text-primary border fw-800">{{ $category->products_count }}</span>
                                    </div>
                                    <div class="progress rounded-pill shadow-inner" style="height: 8px; background-color: #f1f5f9;">
                                        @php
                                            $percentage = $totalProducts > 0 ? ($category->products_count / $totalProducts) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    <i class="fa-solid fa-folder-open fa-2x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-bold">No categories found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Tab -->
        <div class="tab-pane fade" id="sales" role="tabpanel">
            
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label text-success">Revenue</span>
                            <div class="stat-icon icon-success"><i class="fa-solid fa-money-bill-wave"></i></div>
                        </div>
                        <h3 class="stat-value text-success" id="totalRevenueValue">₱{{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Total Transactions</span>
                            <div class="stat-icon icon-info"><i class="fa-solid fa-receipt"></i></div>
                        </div>
                        <h3 class="stat-value" id="totalTransactionsValue">{{ number_format($totalTransactions) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label text-primary">Average Order</span>
                            <div class="stat-icon icon-primary"><i class="fa-solid fa-chart-line"></i></div>
                        </div>
                        <h3 class="stat-value text-primary" id="avgOrderValue">₱{{ number_format($averageOrderValue, 2) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label text-warning">Best Seller</span>
                            <div class="stat-icon icon-warning"><i class="fa-solid fa-award"></i></div>
                        </div>
                        <div class="d-flex flex-column">
                            <h4 class="fw-bold mb-0 text-truncate text-dark" id="bestSellerName" style="font-size: 1.1rem;">{{ $mostSoldProduct->name ?? 'N/A' }}</h4>
                            <small class="text-muted fw-bold" id="bestSellerSold">{{ number_format($mostSoldProduct->total_sold ?? 0) }} sold</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="d-flex justify-content-center mb-4">
                <div class="filter-btn-group" role="group" aria-label="Sales Period Filter" id="salesPeriodFilterGroup">
                    <button type="button" data-period="today" class="btn btn-sm {{ $period == 'today' ? 'active' : '' }}">Today</button>
                    <button type="button" data-period="week" class="btn btn-sm {{ $period == 'week' ? 'active' : '' }}">This Week</button>
                    <button type="button" data-period="month" class="btn btn-sm {{ $period == 'month' ? 'active' : '' }}">This Month</button>
                    <button type="button" data-period="all_time" class="btn btn-sm {{ $period == 'all_time' || !$period ? 'active' : '' }}">All Time</button>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Chart -->
                <div class="col-lg-8">
                    <div class="content-card" id="revenueChartCard">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">Revenue Overview</h5>
                        </div>
                        <div class="card-body-custom">
                            <div style="height: 320px;"><canvas id="revenueChart"></canvas></div>
                        </div>
                    </div>
                </div>
                <!-- Recent Transactions -->
                <div class="col-lg-4">
                     <div class="content-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="card-title-custom mb-0"><i class="fa-solid fa-receipt me-2 text-primary"></i>Recent</h5>
                            <div class="d-flex gap-2 align-items-center">
                                <form action="{{ route('admin.dashboard') }}" method="GET" class="d-inline-block">
                                    <input type="hidden" name="tab" value="sales">
                                    @if(request('period'))
                                        <input type="hidden" name="period" value="{{ request('period') }}">
                                    @endif
                                    <select name="transaction_status" class="form-select form-select-sm border-0 bg-light fw-bold text-secondary rounded-pill px-3" onchange="this.form.submit()" style="width: auto; cursor: pointer; font-size: 0.75rem;">
                                        <option value="all" {{ request('transaction_status') == 'all' ? 'selected' : '' }}>All</option>
                                        <option value="completed" {{ request('transaction_status') == 'completed' ? 'selected' : '' }}>Paid</option>
                                        <option value="returned" {{ request('transaction_status') == 'returned' ? 'selected' : '' }}>Ret</option>
                                    </select>
                                </form>
                                <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-light rounded-circle shadow-sm"><i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                        <div class="card-body-custom p-0" id="recentTransactionsList">
                            <div class="list-group list-group-flush">
                                @forelse($recentTransactions as $transaction)
                                <div class="list-group-item px-4 py-3 border-bottom border-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 rounded-circle p-2 d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 40px; height: 40px;">
                                                <i class="fa-solid fa-file-invoice small"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">#{{ $transaction->id }}</div>
                                                <div class="text-muted small fw-500">{{ $transaction->user->name }}</div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">₱{{ number_format($transaction->total_amount, 2) }}</div>
                                            <div class="text-muted small fw-500">{{ $transaction->created_at->format('M d') }}</div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="p-5 text-center text-muted">
                                    <i class="fa-solid fa-receipt mb-3 fa-2x opacity-25"></i>
                                    <p class="mb-0 fw-bold">No transactions yet</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                     </div>
                </div>
            </div>
        </div>

        <!-- Forecast Tab -->
        <div class="tab-pane fade" id="forecast" role="tabpanel">
            
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label text-success">Fast Moving</span>
                            <div class="stat-icon icon-success"><i class="fa-solid fa-bolt"></i></div>
                        </div>
                        <h3 class="stat-value" id="fastMovingCount">{{ number_format($fastMovingCount) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label text-info">Stable</span>
                            <div class="stat-icon icon-info"><i class="fa-solid fa-equals"></i></div>
                        </div>
                        <h3 class="stat-value" id="stableMovingCount">{{ number_format($stableMovingCount) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label text-secondary">Slow Moving</span>
                            <div class="stat-icon icon-dark" style="background-color: #f3f4f6; color: #1f2937;"><i class="fa-solid fa-hourglass-half"></i></div>
                        </div>
                        <h3 class="stat-value text-secondary" id="slowMovingCount">{{ number_format($slowMovingCount) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label text-danger">Restock Needed</span>
                            <div class="stat-icon icon-danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="stat-value text-danger mb-0" id="restockNeededCount">{{ number_format($restockNeededCount) }}</h3>
                            <a href="{{ route('admin.reports.restock_list') }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" style="font-size: 0.7rem;">
                                <i class="fa-solid fa-print"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="d-flex justify-content-center mb-4">
                <div class="filter-btn-group" role="group" aria-label="Forecast Period Filter" id="forecastPeriodFilterGroup">
                    <button type="button" data-period="today" class="btn btn-sm {{ $period == 'today' ? 'active' : '' }}">Today</button>
                    <button type="button" data-period="week" class="btn btn-sm {{ $period == 'week' ? 'active' : '' }}">This Week</button>
                    <button type="button" data-period="month" class="btn btn-sm {{ $period == 'month' ? 'active' : '' }}">This Month</button>
                    <button type="button" data-period="all_time" class="btn btn-sm {{ $period == 'all_time' || !$period ? 'active' : '' }}">All Time</button>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="card-title-custom mb-0"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Forecast & Suggestions</h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#legendCollapse">
                        <i class="fa-solid fa-circle-info me-1"></i> Legend
                    </button>
                </div>
                
                <div class="collapse px-4 pb-3" id="legendCollapse">
                    <div class="p-3 bg-light rounded-3 border-0 small text-muted">
                        <div class="row g-3">
                            <div class="col-md-6 col-xl-3"><i class="fa-solid fa-angles-up text-success me-2"></i> <strong>Fast Moving:</strong> High turnover</div>
                            <div class="col-md-6 col-xl-3"><i class="fa-solid fa-minus text-primary me-2"></i> <strong>Stable:</strong> Moderate turnover</div>
                            <div class="col-md-6 col-xl-3"><i class="fa-solid fa-angles-down text-secondary me-2"></i> <strong>Slow Moving:</strong> Low turnover</div>
                            <div class="col-md-6 col-xl-3"><i class="fa-solid fa-arrow-trend-up text-danger me-2"></i> <strong>Stock In:</strong> Below demand</div>
                        </div>
                    </div>
                </div>

                <div class="card-body-custom p-0">
                    {{-- Desktop table (hidden on mobile) --}}
                    <div class="d-none d-md-block table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Product</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Velocity</th>
                                    <th class="text-center">Action</th>
                                    <th class="text-center pe-4">Suggested (L/G/O)</th>
                                </tr>
                            </thead>
                            <tbody id="forecastTableBody">
                                @forelse($productForecasts as $forecast)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark fs-6">{{ $forecast['name'] }}</div>
                                            <div class="badge bg-soft-info text-primary small px-2 py-1">{{ $forecast['category_name'] }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">{{ $forecast['stock'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="{{ $forecast['velocity_class'] }} fw-bold">
                                                <i class="fa-solid {{ $forecast['velocity_icon'] }} me-1"></i>
                                                {{ $forecast['velocity_status'] }}
                                            </div>
                                            <div class="text-muted small fw-500">Avg: {{ number_format($forecast['avg_daily_sales'], 2) }}/d</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $forecast['action_class'] == 'text-danger' ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success' }} rounded-pill px-3 py-2 fw-bold">
                                                <i class="fa-solid {{ $forecast['action_icon'] }} me-1"></i>
                                                {{ $forecast['stock_action'] }}
                                                @if($forecast['action_qty'] > 0)
                                                    (+{{ $forecast['action_qty'] }})
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-inline-flex gap-1">
                                                <span class="badge bg-soft-warning text-warning fw-bold" title="Low">{{ $forecast['suggested_low_threshold'] }}</span>
                                                <span class="badge bg-soft-success text-success fw-bold" title="Good">{{ $forecast['suggested_good_stock'] }}</span>
                                                <span class="badge bg-soft-danger text-danger fw-bold" title="Over">{{ $forecast['suggested_overstock_threshold'] }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted opacity-50">
                                            <i class="fa-solid fa-chart-pie fa-2x mb-3"></i>
                                            <p class="mb-0 fw-bold">No forecast data available.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile card list (hidden on desktop) --}}
                    <div class="d-block d-md-none px-3 py-2" id="forecastCardList">
                        @forelse($productForecasts as $forecast)
                            <div class="border rounded-3 p-3 mb-2 bg-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $forecast['name'] }}</div>
                                        <div class="badge bg-soft-info text-primary small px-2 py-1 mt-1">{{ $forecast['category_name'] }}</div>
                                    </div>
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">{{ $forecast['stock'] }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <div class="{{ $forecast['velocity_class'] }} fw-bold small">
                                        <i class="fa-solid {{ $forecast['velocity_icon'] }} me-1"></i>
                                        {{ $forecast['velocity_status'] }}
                                        <span class="text-muted fw-normal ms-1">({{ number_format($forecast['avg_daily_sales'], 2) }}/d)</span>
                                    </div>
                                    <span class="badge {{ $forecast['action_class'] == 'text-danger' ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success' }} rounded-pill px-3 py-1 fw-bold small">
                                        <i class="fa-solid {{ $forecast['action_icon'] }} me-1"></i>
                                        {{ $forecast['stock_action'] }}
                                        @if($forecast['action_qty'] > 0)
                                            (+{{ $forecast['action_qty'] }})
                                        @endif
                                    </span>
                                </div>
                                <div class="mt-2 d-flex align-items-center gap-1">
                                    <span class="text-muted small me-1">Suggested:</span>
                                    <span class="badge bg-soft-warning text-warning fw-bold" title="Low">L: {{ $forecast['suggested_low_threshold'] }}</span>
                                    <span class="badge bg-soft-success text-success fw-bold" title="Good">G: {{ $forecast['suggested_good_stock'] }}</span>
                                    <span class="badge bg-soft-danger text-danger fw-bold" title="Over">O: {{ $forecast['suggested_overstock_threshold'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted opacity-50">
                                <i class="fa-solid fa-chart-pie fa-2x mb-3"></i>
                                <p class="mb-0 fw-bold">No forecast data available.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="p-3 bg-light border-top text-end rounded-bottom-4">
                    <span class="text-muted small fw-500"><i class="fa-solid fa-circle-info me-1"></i> L: Low | G: Good | O: Overstock</span>
                </div>
            </div>
        </div>
    </div> <!-- End Tab Content -->
</div> <!-- End Container -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const adminGuideModal = document.getElementById('adminIntroModal');
        let adminGuideSeenMarked = false;

        const markAdminGuideSeen = async () => {
            if (adminGuideSeenMarked || !adminGuideModal) {
                return;
            }

            adminGuideSeenMarked = true;

            try {
                await fetch(`{{ route('admin.dashboard.admin_guide_seen') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
            } catch (error) {
                console.error('Failed to mark admin guide as seen:', error);
                adminGuideSeenMarked = false;
            }
        };

        if (adminGuideModal) {
            const modal = new bootstrap.Modal(adminGuideModal, { backdrop: false, keyboard: true });
            modal.show();

            adminGuideModal.querySelectorAll('[data-guide-href]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const destination = button.getAttribute('data-guide-href');
                    await markAdminGuideSeen();
                    window.location.href = destination;
                });
            });

            adminGuideModal.addEventListener('hidden.bs.modal', () => {
                markAdminGuideSeen();
            }, { once: true });
        }

        // Persist active tab - URL param takes priority over localStorage
        const urlParams = new URLSearchParams(window.location.search);
        const tabFromUrl = urlParams.get('tab');
        const tabMap = { 'product': '#product', 'sales': '#sales', 'forecast': '#forecast' };
        
        if (tabFromUrl && tabMap[tabFromUrl]) {
            const tabBtn = document.querySelector(`[data-bs-target="${tabMap[tabFromUrl]}"]`);
            if (tabBtn) new bootstrap.Tab(tabBtn).show();
        } else {
            const activeTab = localStorage.getItem('dashboardActiveTab');
            if (activeTab) {
                const tabTrigger = new bootstrap.Tab(document.querySelector(activeTab));
                tabTrigger.show();
            }
        }
        const tabEl = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEl.forEach(el => {
            el.addEventListener('shown.bs.tab', event => {
                localStorage.setItem('dashboardActiveTab', `[data-bs-target="${event.target.dataset.bsTarget}"]`);
            });
        });

        // Chart.js
        let revenueChart = null;
        const isMobileView = () => window.matchMedia('(max-width: 767.98px)').matches;
        const formatChartPeso = (value) => '₱' + Number(value).toLocaleString();
        const compactDateLabel = (label) => {
            const date = new Date(label);
            if (Number.isNaN(date.getTime())) return label;
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        };
        const createRevenueChart = (labels, revenues) => {
            const chartEl = document.getElementById('revenueChart');
            if (!chartEl) return null;
            const mobile = isMobileView();

            if (revenueChart) revenueChart.destroy();
            return new Chart(chartEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: revenues,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.12)',
                        borderWidth: mobile ? 2 : 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointRadius: mobile ? 2 : 4,
                        pointHoverRadius: mobile ? 4 : 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: mobile ? { left: 8, right: 8, top: 4, bottom: 0 } : { left: 0, right: 0, top: 0, bottom: 0 }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: mobile ? 10 : 12,
                            titleFont: { size: mobile ? 11 : 13 },
                            bodyFont: { size: mobile ? 11 : 13 },
                            displayColors: false,
                            callbacks: {
                                label: function(context) { return formatChartPeso(context.parsed.y); }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                maxTicksLimit: mobile ? 5 : 8,
                                padding: mobile ? 6 : 8,
                                font: { size: mobile ? 10 : 12 },
                                callback: function(value) { return formatChartPeso(value); }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: mobile ? 4 : 8,
                                maxRotation: 0,
                                minRotation: 0,
                                font: { size: mobile ? 10 : 12 },
                                callback: function(value) {
                                    const label = this.getLabelForValue(value);
                                    return mobile ? compactDateLabel(label) : label;
                                }
                            }
                        }
                    }
                }
            });
        };
        revenueChart = createRevenueChart({!! json_encode($dates) !!}, {!! json_encode($revenues) !!});

        // Helpers
        const peso = (n) => '₱' + Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const setActive = (container, value) => {
            container.querySelectorAll('.filter-btn').forEach(btn => btn.classList.toggle('active', btn.value === value));
        };
        const animateOnce = (el) => {
            if (!el) return;
            el.classList.remove('animate-fade-up');
            // Force reflow to restart animation
            void el.offsetWidth;
            el.classList.add('animate-fade-up');
            setTimeout(() => el.classList.remove('animate-fade-up'), 600);
        };
        const setLoading = (els, on) => {
            (Array.isArray(els) ? els : [els]).filter(Boolean).forEach(el => {
                el.classList.toggle('is-loading', !!on);
            });
        };

        // SALES: AJAX Filters
        const salesFilterGroup = document.getElementById('salesPeriodFilterGroup');
        const trxStatusSelect = document.querySelector('#sales select[name="transaction_status"]');
        
        async function loadSales(period) {
            const status = trxStatusSelect ? trxStatusSelect.value : 'all';
            const resp = await fetch(`{{ route('admin.dashboard.sales_data') }}?period=${encodeURIComponent(period)}&transaction_status=${encodeURIComponent(status)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await resp.json();
            
            // Update cards
            document.getElementById('totalRevenueValue').textContent = peso(data.totals.revenue);
            document.getElementById('totalTransactionsValue').textContent = (data.totals.transactions).toLocaleString();
            document.getElementById('avgOrderValue').textContent = peso(data.totals.avg_order);
            document.getElementById('bestSellerName').textContent = data.totals.best_seller.name || 'N/A';
            document.getElementById('bestSellerSold').textContent = `${(data.totals.best_seller.sold || 0).toLocaleString()} sold`;
            
            // Animate cards
            animateOnce(document.getElementById('totalRevenueValue'));
            animateOnce(document.getElementById('totalTransactionsValue'));
            animateOnce(document.getElementById('avgOrderValue'));
            animateOnce(document.getElementById('bestSellerName'));
            animateOnce(document.getElementById('bestSellerSold'));
            
            // Update recent list
            const listWrap = document.getElementById('recentTransactionsList');
            if (listWrap) {
                listWrap.innerHTML = data.recent_html;
                animateOnce(listWrap);
            }
            
            // Update chart
            revenueChart = createRevenueChart(data.chart.dates, data.chart.revenues);
            animateOnce(document.getElementById('revenueChartCard'));
        }

        if (salesFilterGroup) {
            salesFilterGroup.addEventListener('click', async (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    e.preventDefault();
                    const period = btn.dataset.period;
                    
                    // Update active state
                    salesFilterGroup.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    const salesStats = document.querySelector('#sales .row.g-4.mb-5');
                    const recent = document.getElementById('recentTransactionsList');
                    const chartCard = document.getElementById('revenueChartCard');
                    
                    setLoading([salesStats, recent, chartCard], true);
                    try {
                        await loadSales(period);
                    } finally {
                        setLoading([salesStats, recent, chartCard], false);
                    }
                }
            });
        }

        if (trxStatusSelect) {
            trxStatusSelect.addEventListener('change', async () => {
                const activeBtn = salesFilterGroup.querySelector('.btn.active');
                const current = activeBtn ? activeBtn.dataset.period : 'all_time';
                
                const salesStats = document.querySelector('#sales .row.g-4.mb-5');
                const recent = document.getElementById('recentTransactionsList');
                const chartCard = document.getElementById('revenueChartCard');
                
                setLoading([salesStats, recent, chartCard], true);
                try {
                    await loadSales(current);
                } finally {
                    setLoading([salesStats, recent, chartCard], false);
                }
            });
        }

        // FORECAST: AJAX Filters
        const forecastFilterGroup = document.getElementById('forecastPeriodFilterGroup');
        
        async function loadForecast(period) {
            const resp = await fetch(`{{ route('admin.dashboard.forecast_data') }}?period=${encodeURIComponent(period)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await resp.json();
            
            // Update counts
            document.getElementById('fastMovingCount').textContent = (data.counts.fast).toLocaleString();
            document.getElementById('stableMovingCount').textContent = (data.counts.stable).toLocaleString();
            document.getElementById('slowMovingCount').textContent = (data.counts.slow).toLocaleString();
            document.getElementById('restockNeededCount').textContent = (data.counts.restock).toLocaleString();
            
            animateOnce(document.getElementById('fastMovingCount'));
            animateOnce(document.getElementById('stableMovingCount'));
            animateOnce(document.getElementById('slowMovingCount'));
            animateOnce(document.getElementById('restockNeededCount'));
            
            // Update table rows
            const tbody = document.getElementById('forecastTableBody');
            if (tbody) {
                tbody.innerHTML = data.rows_html;
                animateOnce(tbody);
            }
        }

        if (forecastFilterGroup) {
            forecastFilterGroup.addEventListener('click', async (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    e.preventDefault();
                    const period = btn.dataset.period;
                    
                    // Update active state
                    forecastFilterGroup.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    const stats = document.querySelector('#forecast .row.g-4.mb-5');
                    const tbody = document.getElementById('forecastTableBody');
                    
                    setLoading([stats, tbody], true);
                    try {
                        await loadForecast(period);
                    } finally {
                        setLoading([stats, tbody], false);
                    }
                }
            });
        }
    });
</script>
@endsection
