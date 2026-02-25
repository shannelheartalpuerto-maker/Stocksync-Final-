@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin-dashboard-design.css') }}?v={{ time() }}" rel="stylesheet">

<div class="container-fluid px-4 admin-dashboard-container animate-fade-up">
    <!-- Header -->
    <div class="dashboard-header">
        <h2 class="dashboard-title"><i class="fa-solid fa-clipboard-list me-3 text-primary"></i>Stock Logs</h2>
        <p class="dashboard-subtitle">Track comprehensive stock movements and history.</p>
    </div>

    <div class="content-card mb-4">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h5 class="card-title-custom mb-0"><i class="fa-solid fa-clock-rotate-left me-2"></i>Stock History & Logs</h5>
            
            <div class="btn-group" role="group" aria-label="Time Period Filter" id="periodFilterGroup">
                <a href="{{ route('admin.stock_logs.index', ['period' => 'today']) }}" data-period="today" class="btn btn-sm btn-outline-primary {{ $period == 'today' ? 'active' : '' }}">Today</a>
                <a href="{{ route('admin.stock_logs.index', ['period' => 'week']) }}" data-period="week" class="btn btn-sm btn-outline-primary {{ $period == 'week' ? 'active' : '' }}">This Week</a>
                <a href="{{ route('admin.stock_logs.index', ['period' => 'month']) }}" data-period="month" class="btn btn-sm btn-outline-primary {{ $period == 'month' ? 'active' : '' }}">This Month</a>
                <a href="{{ route('admin.stock_logs.index', ['period' => 'all']) }}" data-period="all" class="btn btn-sm btn-outline-primary {{ $period == 'all' ? 'active' : '' }}">All Time</a>
            </div>
        </div>

        <div class="card-body-custom">
            <!-- Summary Stats -->
            <div class="row g-4 mb-4">
                <!-- Stock In Summary -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Total Stock In</p>
                                <h3 class="stat-value text-success" id="statStockIn">+{{ number_format($totalStockIn) }}</h3>
                                <small class="text-muted">Items Added</small>
                            </div>
                            <div class="stat-icon success">
                                <i class="fa-solid fa-arrow-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Returned Summary -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Total Returned</p>
                                <h3 class="stat-value text-info" id="statReturned">+{{ number_format($totalReturned) }}</h3>
                                <small class="text-muted">Items Returned</small>
                            </div>
                            <div class="stat-icon info">
                                <i class="fa-solid fa-rotate-left"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Stock Out Summary -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Total Stock Out</p>
                                <h3 class="stat-value text-primary" id="statStockOut">-{{ number_format($totalStockOut) }}</h3>
                                <small class="text-muted">Items Sold/Removed</small>
                            </div>
                            <div class="stat-icon primary">
                                <i class="fa-solid fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Damaged Summary -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Total Damaged</p>
                                <h3 class="stat-value text-danger" id="statDamaged">{{ number_format($totalDamaged) }}</h3>
                                <small class="text-muted">Items Lost</small>
                            </div>
                            <div class="stat-icon danger">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4 dashboard-tabs w-100 justify-content-center border-bottom-0 gap-2 p-1 bg-light rounded-pill d-inline-flex" id="logTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4" id="stock-in-tab" data-bs-toggle="tab" data-bs-target="#stock-in" type="button" role="tab" aria-controls="stock-in" aria-selected="true">
                        <i class="fa-solid fa-arrow-down text-success me-2"></i>Stock In
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab" aria-controls="returned" aria-selected="false">
                        <i class="fa-solid fa-rotate-left text-info me-2"></i>Returned
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="stock-out-tab" data-bs-toggle="tab" data-bs-target="#stock-out" type="button" role="tab" aria-controls="stock-out" aria-selected="false">
                        <i class="fa-solid fa-arrow-up text-primary me-2"></i>Stock Out
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="damaged-tab" data-bs-toggle="tab" data-bs-target="#damaged" type="button" role="tab" aria-controls="damaged" aria-selected="false">
                        <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Damaged
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="logTabsContent">
                <!-- Stock In Tab -->
                <div class="tab-pane fade show active" id="stock-in" role="tabpanel" aria-labelledby="stock-in-tab">
                    <div id="stock-in-content" class="table-responsive">
                        @include('admin.partials.logs_stockin')
                    </div>
                </div>

                <!-- Returned Tab -->
                <div class="tab-pane fade" id="returned" role="tabpanel" aria-labelledby="returned-tab">
                    <div id="returned-content" class="table-responsive">
                        @include('admin.partials.logs_returned')
                    </div>
                </div>

                <!-- Stock Out Tab -->
                <div class="tab-pane fade" id="stock-out" role="tabpanel" aria-labelledby="stock-out-tab">
                    <div id="stock-out-content" class="table-responsive">
                        @include('admin.partials.logs_stockout')
                    </div>
                </div>

                <!-- Damaged Tab -->
                <div class="tab-pane fade" id="damaged" role="tabpanel" aria-labelledby="damaged-tab">
                    <div id="damaged-content" class="table-responsive">
                        @include('admin.partials.logs_damaged')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Persistence
        var activeTab = localStorage.getItem('activeAdminLogTab');
        if (activeTab) {
            var tabTrigger = document.querySelector('#logTabs button[data-bs-target="' + activeTab + '"]');
            if (tabTrigger) {
                var tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }

        var tabEl = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEl.forEach(function(el) {
            el.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem('activeAdminLogTab', event.target.getAttribute('data-bs-target'));
            });
        });

        // AJAX Pagination
        function handlePagination(containerId, contentId) {
            const container = document.getElementById(containerId);
            if (!container) return;

            container.addEventListener('click', function(e) {
                const link = e.target.closest('.pagination .page-link');
                
                if (link) {
                    e.preventDefault();
                    e.stopPropagation();

                    const url = link.getAttribute('href');
                    if (!url) return;

                    // Add opacity to indicate loading
                    const contentDiv = document.getElementById(contentId);
                    contentDiv.style.opacity = '0.5';

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        contentDiv.innerHTML = html;
                        contentDiv.style.opacity = '1';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.location.href = url; // Fallback
                    });
                }
            });
        }

        // Initialize for all tabs
        handlePagination('stock-in', 'stock-in-content');
        handlePagination('returned', 'returned-content');
        handlePagination('stock-out', 'stock-out-content');
        handlePagination('damaged', 'damaged-content');

        // Period filter click (AJAX all tabs + summaries)
        const periodGroup = document.getElementById('periodFilterGroup');
        const stockInContent = document.getElementById('stock-in-content');
        const returnedContent = document.getElementById('returned-content');
        const stockOutContent = document.getElementById('stock-out-content');
        const damagedContent = document.getElementById('damaged-content');
        const statIn = document.getElementById('statStockIn');
        const statRet = document.getElementById('statReturned');
        const statOut = document.getElementById('statStockOut');
        const statDam = document.getElementById('statDamaged');
        function animate(el){ if(!el) return; el.classList.remove('animate-fade-up'); void el.offsetWidth; el.classList.add('animate-fade-up'); setTimeout(()=>el.classList.remove('animate-fade-up'), 600); }

        if (periodGroup) {
            periodGroup.addEventListener('click', function(e) {
                const link = e.target.closest('a[data-period]');
                if (!link) return;
                e.preventDefault();
                const url = link.href;
                // Loading state
                [stockInContent, returnedContent, stockOutContent, damagedContent].forEach(el => el && (el.style.opacity = '0.5'));
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(data => {
                        if (data.tabs) {
                            stockInContent.innerHTML = data.tabs.stockin_html;
                            returnedContent.innerHTML = data.tabs.returned_html;
                            stockOutContent.innerHTML = data.tabs.stockout_html;
                            damagedContent.innerHTML = data.tabs.damaged_html;
                            [stockInContent, returnedContent, stockOutContent, damagedContent].forEach(el => el && (el.style.opacity = '1'));
                            [stockInContent, returnedContent, stockOutContent, damagedContent].forEach(el => animate(el));
                        }
                        if (data.summaries) {
                            statIn.textContent = `+${Number(data.summaries.stock_in).toLocaleString()}`;
                            statRet.textContent = `+${Number(data.summaries.returned).toLocaleString()}`;
                            statOut.textContent = `-${Number(data.summaries.stock_out).toLocaleString()}`;
                            statDam.textContent = Number(data.summaries.damaged).toLocaleString();
                            [statIn, statRet, statOut, statDam].forEach(el => animate(el));
                        }
                        // Update active button styles
                        periodGroup.querySelectorAll('a[data-period]').forEach(a => a.classList.remove('active'));
                        link.classList.add('active');
                    })
                    .catch(err => {
                        console.error(err);
                        window.location.href = url;
                    });
            });
        }
    });
</script>
@endsection
