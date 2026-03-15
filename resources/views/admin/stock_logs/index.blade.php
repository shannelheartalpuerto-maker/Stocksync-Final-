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
}
.inv-header-actions .filter-btn-group {
    border-color: rgba(255,255,255,.45);
    background: rgba(255,255,255,.12);
}
.inv-header-actions .filter-btn-group .btn {
    color: #fff;
    border-right-color: rgba(255,255,255,.35);
}
.inv-header-actions .filter-btn-group .btn.active {
    background: #fff;
    color: #1d4ed8 !important;
}
.inv-header-actions .filter-btn-group .btn:not(.active):hover {
    background: rgba(255,255,255,.2);
}
.logs-stats-shell {
    background: transparent;
    border: 0;
    border-radius: 0;
    box-shadow: none;
    padding: 0;
}
.logs-tabs-wrap {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 0.35rem;
    display: inline-flex;
}
.logs-pane-shell {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
    padding: 0.4rem;
}
@media (min-width: 768px) {
    .logs-page-wrap {
        padding-top: 0.75rem !important;
    }
}
</style>

<div class="staff-container logs-page-wrap animate-fade-up">
    <div class="inv-topbar mb-4">
        <div class="inv-topbar-inner">
            <div class="inv-title-wrap">
                <span class="inv-title-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                <h5 class="inv-title-text">Stock History & Logs</h5>
            </div>
            <div class="inv-header-actions">
                <div class="filter-btn-group" id="periodFilterGroup">
                    <a href="{{ route('admin.stock_logs.index', ['period' => 'today']) }}" data-period="today" class="btn btn-sm {{ $period == 'today' ? 'active' : '' }}">Today</a>
                    <a href="{{ route('admin.stock_logs.index', ['period' => 'week']) }}" data-period="week" class="btn btn-sm {{ $period == 'week' ? 'active' : '' }}">This Week</a>
                    <a href="{{ route('admin.stock_logs.index', ['period' => 'month']) }}" data-period="month" class="btn btn-sm {{ $period == 'month' ? 'active' : '' }}">This Month</a>
                    <a href="{{ route('admin.stock_logs.index', ['period' => 'all']) }}" data-period="all" class="btn btn-sm {{ $period == 'all' ? 'active' : '' }}">All Time</a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-body-custom">
            <!-- Summary Stats -->
            <div class="logs-stats-shell mb-4">
            <div class="row g-3 mb-0">
                <!-- Stock In -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Stock In</span>
                            <div class="stat-icon icon-success">
                                <i class="fa-solid fa-arrow-down"></i>
                            </div>
                        </div>
                        <h3 class="stat-value text-success" id="statStockIn">+{{ number_format($totalStockIn) }}</h3>
                    </div>
                </div>
                <!-- Returned -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Returned</span>
                            <div class="stat-icon icon-info">
                                <i class="fa-solid fa-rotate-left"></i>
                            </div>
                        </div>
                        <h3 class="stat-value text-info" id="statReturned">+{{ number_format($totalReturned) }}</h3>
                    </div>
                </div>
                <!-- Stock Out -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Stock Out</span>
                            <div class="stat-icon icon-primary">
                                <i class="fa-solid fa-arrow-up"></i>
                            </div>
                        </div>
                        <h3 class="stat-value text-primary" id="statStockOut">-{{ number_format($totalStockOut) }}</h3>
                    </div>
                </div>
                <!-- Damaged -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Damaged</span>
                            <div class="stat-icon icon-danger">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                        </div>
                        <h3 class="stat-value text-danger" id="statDamaged">{{ number_format($totalDamaged) }}</h3>
                    </div>
                </div>
            </div>
            </div>

            <!-- Tabs -->
            <div class="d-flex justify-content-center mb-4">
                <ul class="nav nav-pills dashboard-tabs logs-tabs-wrap" id="logTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4 py-2" id="stock-in-tab" data-bs-toggle="tab" data-bs-target="#stock-in" type="button" role="tab" aria-controls="stock-in" aria-selected="true">
                            <i class="fa-solid fa-arrow-down me-2"></i>Stock In
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 py-2" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab" aria-controls="returned" aria-selected="false">
                            <i class="fa-solid fa-rotate-left me-2"></i>Returned
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 py-2" id="stock-out-tab" data-bs-toggle="tab" data-bs-target="#stock-out" type="button" role="tab" aria-controls="stock-out" aria-selected="false">
                            <i class="fa-solid fa-arrow-up me-2"></i>Stock Out
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 py-2" id="damaged-tab" data-bs-toggle="tab" data-bs-target="#damaged" type="button" role="tab" aria-controls="damaged" aria-selected="false">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>Damaged
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="logTabsContent">
                <div class="tab-pane fade show active" id="stock-in" role="tabpanel" aria-labelledby="stock-in-tab">
                    <div class="logs-pane-shell" id="stock-in-content">
                        @include('admin.partials.logs_stockin')
                    </div>
                </div>
                <div class="tab-pane fade" id="returned" role="tabpanel" aria-labelledby="returned-tab">
                    <div class="logs-pane-shell" id="returned-content">
                        @include('admin.partials.logs_returned')
                    </div>
                </div>
                <div class="tab-pane fade" id="stock-out" role="tabpanel" aria-labelledby="stock-out-tab">
                    <div class="logs-pane-shell" id="stock-out-content">
                        @include('admin.partials.logs_stockout')
                    </div>
                </div>
                <div class="tab-pane fade" id="damaged" role="tabpanel" aria-labelledby="damaged-tab">
                    <div class="logs-pane-shell" id="damaged-content">
                        @include('admin.partials.logs_damaged')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function cleanupModalArtifacts() {
            var openModal = document.querySelector('.modal.show');
            var backdrops = document.querySelectorAll('.modal-backdrop');

            backdrops.forEach(function(backdrop, index) {
                if (!openModal || index < backdrops.length - 1) {
                    backdrop.remove();
                }
            });

            if (!openModal) {
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
            }
        }

        function moveTabModalsToBody(contentId) {
            var content = document.getElementById(contentId);
            if (!content) return;

            content.querySelectorAll('.modal[id]').forEach(function(modal) {
                document.querySelectorAll('body > .modal[id]').forEach(function(existing) {
                    if (existing !== modal && existing.id === modal.id) {
                        existing.remove();
                    }
                });

                document.body.appendChild(modal);

                if (!modal.dataset.backdropCleanupBound) {
                    modal.addEventListener('hidden.bs.modal', cleanupModalArtifacts);
                    modal.dataset.backdropCleanupBound = '1';
                }
            });
        }

        function refreshAllLogModals() {
            ['stock-in-content', 'returned-content', 'stock-out-content', 'damaged-content'].forEach(moveTabModalsToBody);
            cleanupModalArtifacts();
        }

        refreshAllLogModals();

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
                        refreshAllLogModals();
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
                            refreshAllLogModals();
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
