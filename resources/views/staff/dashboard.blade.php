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
        <h2 class="dashboard-title"><i class="fa-solid fa-gauge-high me-3 text-primary"></i>Staff Dashboard</h2>
        <p class="dashboard-subtitle">Overview of your daily tasks and performance.</p>
    </div>

    <!-- Navigation Tabs -->
    <div class="d-flex justify-content-center w-100">
        <ul class="nav nav-pills dashboard-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="product-tab" data-bs-toggle="tab" data-bs-target="#product" type="button" role="tab" aria-controls="product" aria-selected="true">
                    <i class="fa-solid fa-box"></i>Product Data
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab" aria-controls="sales" aria-selected="false">
                    <i class="fa-solid fa-chart-line"></i>Sales Analytics
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="forecast-tab" data-bs-toggle="tab" data-bs-target="#forecast" type="button" role="tab" aria-controls="forecast" aria-selected="false">
                    <i class="fa-solid fa-bell"></i>Stock Alerts
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="dashboardTabsContent">
        <!-- Product Data Tab -->
        <div class="tab-pane fade show active" id="product" role="tabpanel" aria-labelledby="product-tab">
            <!-- Product Stats -->
            <div class="row g-4 mb-4">
                 <!-- Total Products -->
                 <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Total Products</p>
                                <h3 class="stat-value">{{ number_format($totalProducts) }}</h3>
                            </div>
                            <div class="stat-icon primary">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                        </div>
                    </div>
                 </div>
                 <!-- Total Stock -->
                 <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Total Stock</p>
                                <h3 class="stat-value">{{ number_format($totalStock) }}</h3>
                            </div>
                            <div class="stat-icon info">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                        </div>
                    </div>
                 </div>
                 <!-- Total Categories -->
                 <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Total Categories</p>
                                <h3 class="stat-value">{{ number_format($totalCategories) }}</h3>
                            </div>
                            <div class="stat-icon warning">
                                <i class="fa-solid fa-tags"></i>
                            </div>
                        </div>
                    </div>
                 </div>
                 <!-- Top Moving Product -->
                 <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="min-width: 0;">
                                <p class="stat-label">Top Mover</p>
                                <h5 class="fw-bold mb-0 text-truncate">{{ $topMovingProduct }}</h5>
                            </div>
                            <div class="stat-icon success">
                                <i class="fa-solid fa-fire"></i>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>

            <!-- Actionable Inventory Alerts -->
            @if($outOfStockCount > 0 || $lowStockCount > 0)
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
                <i class="fa-solid fa-triangle-exclamation fa-2x me-3 text-warning"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Attention Required</h5>
                    <p class="mb-0">
                        You have <strong>{{ $outOfStockCount }}</strong> items out of stock and <strong>{{ $lowStockCount }}</strong> items running low.
                        <a href="{{ route('staff.inventory') }}?search=" class="fw-bold text-dark text-decoration-underline">Check Inventory</a>
                    </p>
                </div>
            </div>
            @endif

            <!-- Stock Level Monitoring Table -->
            <div class="content-card">
                <div class="card-header-custom">
                    <h5 class="card-title-custom"><i class="fa-solid fa-chart-simple me-2"></i>Stock Level Monitoring</h5>
                </div>
                <div class="card-body-custom">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Stock Level</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockLevels as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="rounded me-3" width="40" height="40" style="object-fit: cover;" onerror="this.parentNode.innerHTML='<div class=\'rounded me-3 d-flex align-items-center justify-content-center bg-light text-secondary\' style=\'width: 40px; height: 40px;\'><i class=\'fa-solid fa-box\'></i></div>'">
                                            @else
                                                <div class="rounded me-3 d-flex align-items-center justify-content-center bg-light text-secondary" style="width: 40px; height: 40px;">
                                                    <i class="fa-solid fa-box"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $product->name }}</h6>
                                                <small class="text-muted">{{ $product->code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->category)
                                            <span class="badge bg-light text-dark border">{{ $product->category->name }}</span>
                                        @else
                                            <span class="text-muted small">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->brand)
                                            <span class="badge bg-light text-dark border">{{ $product->brand->name }}</span>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td style="width: 30%;">
                                        <div class="progress" style="height: 6px;">
                                            @php
                                                $maxVal = max($product->overstock_threshold, 100);
                                                $percentage = ($product->quantity / $maxVal) * 100;
                                                $color = 'success'; // Good
                                                if($product->quantity <= 0 || $product->quantity < $product->low_stock_threshold) $color = 'danger'; // Out or Low
                                                elseif($product->quantity > $product->overstock_threshold) $color = 'warning'; // Over
                                            @endphp
                                            <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ min($percentage, 100) }}%" aria-valuenow="{{ $product->quantity }}" aria-valuemin="0" aria-valuemax="{{ $maxVal }}"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $product->quantity }}</span>
                                    </td>
                                    <td>
                                        @if($product->quantity <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($product->quantity < $product->low_stock_threshold)
                                            <span class="badge bg-danger">Low Stock</span>
                                        @elseif($product->quantity > $product->overstock_threshold)
                                            <span class="badge bg-warning text-dark">Overstock</span>
                                        @else
                                            <span class="badge bg-success">Good Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No stock data available.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Tab -->
        <div class="tab-pane fade" id="sales" role="tabpanel" aria-labelledby="sales-tab">
            <!-- Today's Performance -->
            <div class="row g-4 mb-4">
                <!-- Today's Revenue -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Today's Revenue</p>
                                <h3 class="stat-value text-success">₱{{ number_format($todayRevenue, 2) }}</h3>
                            </div>
                            <div class="stat-icon primary">
                                <i class="fa-solid fa-calendar-day"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Transactions Today -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Transactions Today</p>
                                <h3 class="stat-value">{{ number_format($todayTransactions) }}</h3>
                            </div>
                            <div class="stat-icon info">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Revenue -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">My Total Sales</p>
                                <h3 class="stat-value text-primary">₱{{ number_format($totalRevenue, 2) }}</h3>
                            </div>
                            <div class="stat-icon success">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Average Sale -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">My Avg Sale</p>
                                <h3 class="stat-value">₱{{ number_format($averageSale, 2) }}</h3>
                            </div>
                            <div class="stat-icon warning">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <!-- Sales Chart -->
                <div class="col-lg-8">
                    <div class="content-card">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">Sales Trend (Last 7 Days)</h5>
                        </div>
                        <div class="card-body-custom">
                            <div style="height: 300px;"><canvas id="salesChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <!-- Top Sellers (Full Height) -->
                <div class="col-lg-4">
                    <div class="content-card">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">Top Sellers</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="topSellersDropdownBtn">
                                    <i class="fa-solid fa-calendar me-1 text-muted"></i>
                                    <span id="topSellersLabel">
                                    @switch($topSellersPeriod)
                                        @case('week') This Week @break
                                        @case('month') This Month @break
                                        @case('all_time') All Time @break
                                        @default Today
                                    @endswitch
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" id="topSellersDropdownMenu">
                                    <li><a class="dropdown-item top-sellers-filter {{ $topSellersPeriod == 'today' ? 'active' : '' }}" href="#" data-period="today" data-label="Today">Today</a></li>
                                    <li><a class="dropdown-item top-sellers-filter {{ $topSellersPeriod == 'week' ? 'active' : '' }}" href="#" data-period="week" data-label="This Week">This Week</a></li>
                                    <li><a class="dropdown-item top-sellers-filter {{ $topSellersPeriod == 'month' ? 'active' : '' }}" href="#" data-period="month" data-label="This Month">This Month</a></li>
                                    <li><a class="dropdown-item top-sellers-filter {{ $topSellersPeriod == 'all_time' ? 'active' : '' }}" href="#" data-period="all_time" data-label="All Time">All Time</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body-custom p-0 overflow-auto" style="max-height: 350px;">
                            <div id="top-sellers-list">
                                @include('staff.partials.top_sellers_list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History Table -->
            <div class="content-card">
                <div class="card-header-custom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 w-100">
                        <h5 class="card-title-custom mb-0">Transaction History</h5>
                        <form action="{{ route('staff.dashboard') }}" method="GET" class="d-flex gap-2 flex-wrap">
                            <input type="hidden" name="tab" value="sales"> <!-- To keep the tab active if JS handles it, or for server-side tab persistence -->
                            <div class="input-group input-group-sm" style="width: auto;">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-calendar text-muted"></i></span>
                                <input type="date" name="start_date" class="form-control border-start-0 ps-0" value="{{ request('start_date') }}" placeholder="Start Date" title="Start Date">
                            </div>
                            <div class="input-group input-group-sm" style="width: auto;">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-calendar text-muted"></i></span>
                                <input type="date" name="end_date" class="form-control border-start-0 ps-0" value="{{ request('end_date') }}" placeholder="End Date" title="End Date">
                            </div>
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" value="{{ request('search') }}" placeholder="Search ID...">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i></button>
                            <a href="{{ route('staff.dashboard') }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                        </form>
                    </div>
                </div>
                <div class="card-body-custom">
                    <div id="transactions-container">
                        @include('staff.partials.transactions_table')
                    </div>
                </div>
            </div>
        </div>

        <!-- Forecast Tab -->
        <div class="tab-pane fade" id="forecast" role="tabpanel" aria-labelledby="forecast-tab">
            <div class="row g-4 mb-4">
                 <!-- Low Stock -->
                 <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Low Stock</p>
                                <h3 class="stat-value text-danger">{{ number_format($lowStockCount) }}</h3>
                                <small class="text-muted">Items Low</small>
                            </div>
                            <div class="stat-icon danger">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                        </div>
                    </div>
                 </div>

                 <!-- Good Stock -->
                 <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Good Stock</p>
                                <h3 class="stat-value text-success">{{ number_format($goodStockCount) }}</h3>
                                <small class="text-muted">Healthy Level</small>
                            </div>
                            <div class="stat-icon success">
                                <i class="fa-solid fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                 </div>

                 <!-- Over Stock -->
                 <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Over Stock</p>
                                <h3 class="stat-value text-info">{{ number_format($overStockCount) }}</h3>
                                <small class="text-muted">Excess Items</small>
                            </div>
                            <div class="stat-icon info">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                        </div>
                    </div>
                 </div>

                 <!-- Out of Stock -->
                 <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Out of Stock</p>
                                <h3 class="stat-value text-secondary">{{ number_format($outOfStockCount) }}</h3>
                                <small class="text-muted">Action Needed</small>
                            </div>
                            <div class="stat-icon warning">
                                <i class="fa-solid fa-ban"></i>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>
            
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex">
                    <i class="fa-solid fa-bell fa-2x me-3 text-info"></i>
                    <div>
                        <h5 class="alert-heading fw-bold">Stock Insight</h5>
                        <p class="mb-0">
                            You are selling approximately <strong>{{ number_format($totalForecastQty / 7, 1) }}</strong> units per day. 
                            Ensure high-velocity items like <strong>{{ $topMovingProduct }}</strong> are well-stocked for the weekend.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Persistence
        const tabLinks = document.querySelectorAll('#dashboardTabs button[data-bs-toggle="tab"]');
        tabLinks.forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function (event) {
                localStorage.setItem('activeStaffDashboardTab', event.target.id);
            });
        });

        // Check for tab query parameter first
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        let activeTabId = null;

        if (tabParam) {
            activeTabId = `${tabParam}-tab`;
            // Update localStorage to match current explicit navigation
            localStorage.setItem('activeStaffDashboardTab', activeTabId);
        } else {
            activeTabId = localStorage.getItem('activeStaffDashboardTab');
        }

        if (activeTabId) {
            const activeTab = document.getElementById(activeTabId);
            if (activeTab) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    const tabInstance = new bootstrap.Tab(activeTab);
                    tabInstance.show();
                } else {
                    activeTab.click();
                }
            }
        }

        // Top Sellers AJAX Filter
        const topSellersLinks = document.querySelectorAll('.top-sellers-filter');
        const topSellersList = document.getElementById('top-sellers-list');
        const topSellersLabel = document.getElementById('topSellersLabel');
        const topSellersDropdownMenu = document.getElementById('topSellersDropdownMenu');

        topSellersLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const period = this.getAttribute('data-period');
                const label = this.getAttribute('data-label');
                
                // Update Active State
                topSellersDropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
                
                // Update Label
                topSellersLabel.textContent = label;
                
                // Show loading state
                topSellersList.style.opacity = '0.5';
                
                // Fetch Data
                fetch(`{{ route('staff.dashboard') }}?fetch_top_sellers=1&top_sellers_period=${period}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    topSellersList.innerHTML = html;
                    topSellersList.style.opacity = '1';
                })
                .catch(error => {
                    console.error('Error fetching top sellers:', error);
                    topSellersList.style.opacity = '1';
                });
            });
        });

        // AJAX Pagination for Transactions
        const transactionsContainer = document.getElementById('transactions-container');
        if (transactionsContainer) {
            transactionsContainer.addEventListener('click', function(e) {
                const link = e.target.closest('.pagination .page-link');
                if (link) {
                    e.preventDefault();
                    e.stopPropagation();

                    const url = link.getAttribute('href');
                    if (!url || url === '#') return;
                    
                    transactionsContainer.style.opacity = '0.5';
                    
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        transactionsContainer.innerHTML = html;
                        transactionsContainer.style.opacity = '1';
                    })
                    .catch(error => {
                        console.error('Error loading transactions:', error);
                        transactionsContainer.style.opacity = '1';
                        window.location.href = url;
                    });
                }
            });
        }

        const ctx = document.getElementById('salesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($dates),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($revenues),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#4f46e5',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 14 },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: '#f1f5f9' },
                            ticks: { callback: function(value) { return '₱' + value; } }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
