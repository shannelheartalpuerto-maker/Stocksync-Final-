@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin-dashboard-design.css') }}?v={{ time() }}" rel="stylesheet">
<div class="container-fluid px-4 admin-dashboard-container animate-fade-up">
    <!-- Header -->
    <div class="dashboard-header text-center mb-4">
        <h2 class="dashboard-title"><i class="fa-solid fa-clipboard-list me-3"></i>My Stock Logs</h2>
        <p class="dashboard-subtitle">Track your personal stock movements and history.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="content-card">
                <div class="card-header-custom py-3">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i>Stock History & Logs</h5>
                </div>

                <div class="card-body-custom">
                    <!-- Filter & Summary Section -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                        <h5 class="mb-3 mb-md-0 fw-bold text-dark">Activity Summary</h5>
                        <div class="btn-group" role="group" aria-label="Time Period Filter" id="staffPeriodFilterGroup">
                            <a href="{{ route('staff.logs', ['period' => 'today']) }}" data-period="today" class="btn btn-outline-primary {{ $period == 'today' ? 'active' : '' }}">Today</a>
                            <a href="{{ route('staff.logs', ['period' => 'week']) }}" data-period="week" class="btn btn-outline-primary {{ $period == 'week' ? 'active' : '' }}">This Week</a>
                            <a href="{{ route('staff.logs', ['period' => 'month']) }}" data-period="month" class="btn btn-outline-primary {{ $period == 'month' ? 'active' : '' }}">This Month</a>
                            <a href="{{ route('staff.logs', ['period' => 'all']) }}" data-period="all" class="btn btn-outline-primary {{ $period == 'all' ? 'active' : '' }}">All Time</a>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <!-- Returned Summary -->
                        <div class="col-12 col-md-4">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="stat-label">Total Returned</p>
                                        <h3 class="stat-value text-success" id="staffStatReturned">+{{ number_format($totalReturned) }}</h3>
                                        <small class="text-muted">Items Restocked</small>
                                    </div>
                                    <div class="stat-icon success">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Stock Out Summary -->
                        <div class="col-12 col-md-4">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="stat-label">Total Stock Out</p>
                                        <h3 class="stat-value text-primary" id="staffStatStockOut">-{{ number_format($totalStockOut) }}</h3>
                                        <small class="text-muted">Items Sold/Removed</small>
                                    </div>
                                    <div class="stat-icon primary">
                                        <i class="fa-solid fa-arrow-up"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Damaged Summary -->
                        <div class="col-12 col-md-4">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="stat-label">Total Damaged</p>
                                        <h3 class="stat-value text-danger" id="staffStatDamaged">{{ number_format($totalDamaged) }}</h3>
                                        <small class="text-muted">Items Lost</small>
                                    </div>
                                    <div class="stat-icon danger">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-pills dashboard-tabs mb-4 d-flex justify-content-center" id="logTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab" aria-controls="returned" aria-selected="true">
                                <i class="fa-solid fa-rotate-left me-2"></i>Returned
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="stock-out-tab" data-bs-toggle="tab" data-bs-target="#stock-out" type="button" role="tab" aria-controls="stock-out" aria-selected="false">
                                <i class="fa-solid fa-arrow-up me-2"></i>Stock Out
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="damaged-tab" data-bs-toggle="tab" data-bs-target="#damaged" type="button" role="tab" aria-controls="damaged" aria-selected="false">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>Damaged
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="logTabsContent">
                        <!-- Returned Tab -->
                        <div class="tab-pane fade show active" id="returned" role="tabpanel" aria-labelledby="returned-tab">
                            <div id="returned-content">
                                @include('staff.partials.logs_returned')
                            </div>
                        </div>

                        <!-- Stock Out Tab -->
                        <div class="tab-pane fade" id="stock-out" role="tabpanel" aria-labelledby="stock-out-tab">
                            <div id="stock-out-content">
                                @include('staff.partials.logs_stockout')
                            </div>
                        </div>

                        <!-- Damaged Tab -->
                        <div class="tab-pane fade" id="damaged" role="tabpanel" aria-labelledby="damaged-tab">
                            <div id="damaged-content">
                                @include('staff.partials.logs_damaged')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Persistence
        var activeTab = localStorage.getItem('activeLogTab');
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
                localStorage.setItem('activeLogTab', event.target.getAttribute('data-bs-target'));
            });
        });

        // AJAX Pagination for Logs
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
                        // Re-scroll to top of table if needed, or just stay put
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.location.href = url; // Fallback
                    });
                }
            });
        }

        // Initialize for all tabs
        handlePagination('returned', 'returned-content');
        handlePagination('stock-out', 'stock-out-content');
        handlePagination('damaged', 'damaged-content');

        // Period filter: update all tabs and summaries with light animations
        const periodGroup = document.getElementById('staffPeriodFilterGroup');
        const returnedContent = document.getElementById('returned-content');
        const stockOutContent = document.getElementById('stock-out-content');
        const damagedContent = document.getElementById('damaged-content');
        const statReturned = document.getElementById('staffStatReturned');
        const statStockOut = document.getElementById('staffStatStockOut');
        const statDamaged = document.getElementById('staffStatDamaged');
        function anim(el){ if(!el) return; el.classList.remove('animate-fade-up'); void el.offsetWidth; el.classList.add('animate-fade-up'); setTimeout(()=>el.classList.remove('animate-fade-up'), 600); }

        if (periodGroup) {
            periodGroup.addEventListener('click', function(e) {
                const link = e.target.closest('a[data-period]');
                if (!link) return;
                e.preventDefault();
                const url = link.href;
                [returnedContent, stockOutContent, damagedContent].forEach(el => el && (el.style.opacity = '0.5'));
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(data => {
                        if (data.tabs) {
                            returnedContent.innerHTML = data.tabs.returned_html;
                            stockOutContent.innerHTML = data.tabs.stockout_html;
                            damagedContent.innerHTML = data.tabs.damaged_html;
                            [returnedContent, stockOutContent, damagedContent].forEach(el => { el.style.opacity = '1'; anim(el); });
                        }
                        if (data.summaries) {
                            statReturned.textContent = `+${Number(data.summaries.returned).toLocaleString()}`;
                            statStockOut.textContent = `-${Number(data.summaries.stock_out).toLocaleString()}`;
                            statDamaged.textContent = Number(data.summaries.damaged).toLocaleString();
                            [statReturned, statStockOut, statDamaged].forEach(anim);
                        }
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
