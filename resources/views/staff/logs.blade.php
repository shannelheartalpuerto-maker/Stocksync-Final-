@extends('layouts.app')

@section('content')
@push('styles')
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
    .period-filter-pill {
        background: #f3f4f6;
        padding: 4px;
        border-radius: 12px;
        display: inline-flex;
    }
    .period-filter-pill .btn {
        border: none;
        border-radius: 9px;
        padding: 6px 16px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
        transition: all 0.2s;
    }
    .period-filter-pill .btn.active {
        background: white;
        color: var(--primary-color);
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .log-tabs-modern {
        border-bottom: 2px solid var(--border-color);
        gap: 2rem;
    }
    .log-tabs-modern .nav-link {
        border: none;
        background: none;
        padding: 1rem 0.5rem;
        font-weight: 600;
        color: var(--text-secondary);
        position: relative;
        transition: color 0.3s;
    }
    .log-tabs-modern .nav-link.active {
        color: var(--primary-color);
    }
    .log-tabs-modern .nav-link.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--primary-color);
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
@endpush

<div class="container-fluid px-4 staff-container logs-page-wrap animate-fade-up">
    <div class="inv-topbar mb-4">
        <div class="inv-topbar-inner">
            <div class="inv-title-wrap">
                <span class="inv-title-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                <h5 class="inv-title-text">Stock History & Logs</h5>
            </div>
            <div class="inv-header-actions">
                <div class="filter-btn-group" id="staffPeriodFilterGroup">
                    <a href="{{ route('staff.logs', ['period' => 'today']) }}" data-period="today" class="btn btn-sm {{ $period == 'today' ? 'active' : '' }}">Today</a>
                    <a href="{{ route('staff.logs', ['period' => 'week']) }}" data-period="week" class="btn btn-sm {{ $period == 'week' ? 'active' : '' }}">This Week</a>
                    <a href="{{ route('staff.logs', ['period' => 'month']) }}" data-period="month" class="btn btn-sm {{ $period == 'month' ? 'active' : '' }}">This Month</a>
                    <a href="{{ route('staff.logs', ['period' => 'all']) }}" data-period="all" class="btn btn-sm {{ $period == 'all' ? 'active' : '' }}">All Time</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <!-- Stat Cards Section (Matching Dashboard) -->
            <div class="row g-4 mb-5">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Total Returned</span>
                            <div class="stat-icon icon-success">
                                <i class="fa-solid fa-rotate-left"></i>
                            </div>
                        </div>
                        <h3 class="stat-value text-success" id="staffStatReturned">+{{ number_format($totalReturned) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Total Stock Out</span>
                            <div class="stat-icon icon-primary">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                        </div>
                        <h3 class="stat-value text-primary" id="staffStatStockOut">-{{ number_format($totalStockOut) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Total Damaged</span>
                            <div class="stat-icon icon-danger">
                                <i class="fa-solid fa-burst"></i>
                            </div>
                        </div>
                        <h3 class="stat-value text-danger" id="staffStatDamaged">{{ number_format($totalDamaged) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Activity Count</span>
                            <div class="stat-icon icon-info">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                        </div>
                        <h3 class="stat-value text-info" id="staffStatEntries">{{ number_format($totalEntries) }}</h3>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-body-custom">
                    <div class="d-flex justify-content-center mb-4">
                    <ul class="nav nav-pills dashboard-tabs logs-tabs-wrap" id="logTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4 py-2" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab">
                                <i class="fa-solid fa-rotate-left me-2"></i>Returned
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-2" id="stock-out-tab" data-bs-toggle="tab" data-bs-target="#stock-out" type="button" role="tab">
                                <i class="fa-solid fa-arrow-up-from-bracket me-2"></i>Stock Out
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-2" id="damaged-tab" data-bs-toggle="tab" data-bs-target="#damaged" type="button" role="tab">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>Damaged
                            </button>
                        </li>
                    </ul>
                    </div>

                    <div class="tab-content pt-2" id="logTabsContent">
                        <!-- Returned Tab -->
                        <div class="tab-pane fade show active" id="returned" role="tabpanel">
                            <div id="returned-content" class="animate-fade-up logs-pane-shell">
                                @include('staff.partials.logs_returned')
                            </div>
                        </div>

                        <!-- Stock Out Tab -->
                        <div class="tab-pane fade" id="stock-out" role="tabpanel">
                            <div id="stock-out-content" class="animate-fade-up logs-pane-shell">
                                @include('staff.partials.logs_stockout')
                            </div>
                        </div>

                        <!-- Damaged Tab -->
                        <div class="tab-pane fade" id="damaged" role="tabpanel">
                            <div id="damaged-content" class="animate-fade-up logs-pane-shell">
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
            ['returned-content', 'stock-out-content', 'damaged-content'].forEach(moveTabModalsToBody);
            cleanupModalArtifacts();
        }

        refreshAllLogModals();

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
                        refreshAllLogModals();
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
        const statEntries = document.getElementById('staffStatEntries');
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
                            refreshAllLogModals();
                        }
                        if (data.summaries) {
                            statReturned.textContent = `+${Number(data.summaries.returned).toLocaleString()}`;
                            statStockOut.textContent = `-${Number(data.summaries.stock_out).toLocaleString()}`;
                            statDamaged.textContent = Number(data.summaries.damaged).toLocaleString();
                            if(statEntries) statEntries.textContent = Number(data.summaries.total_entries).toLocaleString();
                            [statReturned, statStockOut, statDamaged, statEntries].forEach(anim);
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
