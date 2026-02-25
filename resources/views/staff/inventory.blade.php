@extends('layouts.app')

@section('content')
@push('styles')
<link href="{{ asset('css/admin-dashboard-design.css') }}?v={{ time() }}" rel="stylesheet">
<style>
    /* Force Z-Index Hierarchy to prevent black shading */
    .modal-backdrop {
        z-index: 10040 !important;
    }
    .modal {
        z-index: 10050 !important;
    }
    #scannerModal {
        z-index: 10060 !important;
    }
</style>
@endpush
<div class="container-fluid px-4 admin-dashboard-container animate-fade-up">
    <!-- Header -->
    <div class="dashboard-header text-center mb-4">
        <h2 class="dashboard-title"><i class="fa-solid fa-boxes-stacked me-3"></i>Inventory Management</h2>
        <p class="dashboard-subtitle">Check stock levels and product details.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="content-card">
                <div class="card-header-custom py-3">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-list me-2"></i>Inventory List</h5>
                </div>

                <div class="card-body-custom">
                    <!-- Filter Form -->
                    <form action="{{ route('staff.inventory') }}" method="GET" class="mb-4" id="staffInventoryFilterForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                                    <input type="text" name="search" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search name or code..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="button" id="scanBtn" title="Scan Barcode">
                                        <i class="fa-solid fa-barcode"></i>
                                    </button>
                                    <button class="btn btn-outline-primary" type="submit">Search</button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select" onchange="this.form.submit()">
                                    <option value="all">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="all">All Status</option>
                                    <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                                    <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="good" {{ request('status') == 'good' ? 'selected' : '' }}>Good Stock</option>
                                    <option value="over" {{ request('status') == 'over' ? 'selected' : '' }}>Overstock</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="brand" class="form-select" onchange="this.form.submit()">
                                    <option value="all">All Brands</option>
                                    @isset($brands)
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('staff.inventory') }}" class="btn btn-outline-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div id="inventory-container" class="animate-fade-up">
                        @include('staff.partials.inventory_table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inventoryContainer = document.getElementById('inventory-container');
        const filterForm = document.getElementById('staffInventoryFilterForm');
        const anim = (el) => { if(!el) return; el.classList.remove('animate-fade-up'); void el.offsetWidth; el.classList.add('animate-fade-up'); setTimeout(()=>el.classList.remove('animate-fade-up'), 600); };

        // Helper to move modals to body (fixes stacking context issues)
        function moveModalsToBody() {
            if (!inventoryContainer) return;
            const modals = inventoryContainer.querySelectorAll('.modal');
            modals.forEach(modal => {
                document.body.appendChild(modal);
            });
        }

        // Intercept filter form submit for AJAX load + animation
        if (filterForm && inventoryContainer) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const url = filterForm.action + (filterForm.action.includes('?') ? '&' : '?') + new URLSearchParams(new FormData(filterForm)).toString();
                inventoryContainer.style.opacity = '0.5';
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(resp => resp.text())
                .then(html => {
                    cleanupOldModals();
                    inventoryContainer.innerHTML = html;
                    inventoryContainer.style.opacity = '1';
                    anim(inventoryContainer);
                    moveModalsToBody();
                })
                .catch(err => {
                    console.error(err);
                    filterForm.submit();
                });
            });
        }

        // Helper to remove old modals before loading new content
        function cleanupOldModals() {
            // Remove any existing inventory modals that were moved to body
            const oldModals = document.querySelectorAll('div[id^="reportDamageModal"]');
            oldModals.forEach(modal => modal.remove());
            
            // Cleanup backdrops just in case
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }

        // Initial move on page load
        moveModalsToBody();

        if (inventoryContainer) {
            inventoryContainer.addEventListener('click', function(e) {
                // Target pagination links specifically
                const link = e.target.closest('.pagination .page-link');
                if (link) {
                    e.preventDefault();
                    e.stopPropagation(); // Stop bubbling
                    
                    const url = link.getAttribute('href');
                    if (!url || url === '#') return;

                    inventoryContainer.style.opacity = '0.5';
                    
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
                        // 1. Cleanup old modals (from previous page)
                        cleanupOldModals();

                        // 2. Update content
                        inventoryContainer.innerHTML = html;
                        inventoryContainer.style.opacity = '1';
                        anim(inventoryContainer);
                        
                        // 3. Move new modals to body
                        moveModalsToBody();
                    })
                    .catch(error => {
                        console.error('Error loading inventory:', error);
                        inventoryContainer.style.opacity = '1';
                        // Fallback to normal navigation if fetch fails
                        window.location.href = url;
                    });
                }
            });
        }
    });
</script>

@endsection

@push('scripts')
<!-- Scanner Modal -->
<div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scannerModalLabel"><i class="fa-solid fa-barcode me-2"></i>Scan Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="position-relative bg-dark rounded overflow-hidden">
                    <video id="video" class="w-100" style="max-height: 300px; object-fit: cover;"></video>
                    <div class="scanner-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                        <div style="width: 70%; height: 50%; border: 2px solid rgba(255,255,255,0.8); box-shadow: 0 0 0 9999px rgba(0,0,0,0.5); border-radius: 8px;"></div>
                        <div style="position: absolute; top: 50%; left: 15%; right: 15%; height: 2px; background: rgba(255, 0, 0, 0.8); box-shadow: 0 0 4px red;"></div>
                    </div>
                </div>
                <div class="mt-3">
                    <select id="sourceSelect" class="form-select mb-2" style="display: none;"></select>
                    <p class="text-muted small mb-0" id="scannerStatus">Initializing camera...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let codeReader;
        let selectedDeviceId;
        const scanBtn = document.getElementById('scanBtn');
        const modalElement = document.getElementById('scannerModal');
        const scannerModal = new bootstrap.Modal(modalElement);
        const searchInput = document.getElementById('searchInput');
        const sourceSelect = document.getElementById('sourceSelect');
        const statusText = document.getElementById('scannerStatus');
        
        if(scanBtn) {
            scanBtn.addEventListener('click', function () {
                scannerModal.show();
            });
        }

        modalElement.addEventListener('shown.bs.modal', function () {
            if (typeof ZXing === 'undefined') {
                statusText.textContent = "Scanner library not loaded.";
                return;
            }

            codeReader = new ZXing.BrowserMultiFormatReader();
            statusText.textContent = "Starting camera...";
            
            codeReader.listVideoInputDevices()
                .then((videoInputDevices) => {
                    sourceSelect.innerHTML = '';
                    if (videoInputDevices.length >= 1) {
                        videoInputDevices.forEach((element) => {
                            const sourceOption = document.createElement('option');
                            sourceOption.text = element.label;
                            sourceOption.value = element.deviceId;
                            sourceSelect.appendChild(sourceOption);
                        });

                        sourceSelect.onchange = () => {
                            selectedDeviceId = sourceSelect.value;
                            startScanning(selectedDeviceId);
                        };

                        if(videoInputDevices.length > 1) {
                            sourceSelect.style.display = 'block';
                        }

                        // Prefer back camera
                        const backCamera = videoInputDevices.find(device => device.label.toLowerCase().includes('back') || device.label.toLowerCase().includes('environment'));
                        selectedDeviceId = backCamera ? backCamera.deviceId : videoInputDevices[0].deviceId;
                        
                        sourceSelect.value = selectedDeviceId;
                        startScanning(selectedDeviceId);
                    } else {
                        statusText.textContent = "No camera found.";
                    }
                })
                .catch((err) => {
                    console.error(err);
                    statusText.textContent = "Error accessing camera: " + err;
                });
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            if (codeReader) {
                codeReader.reset();
                codeReader = null;
            }
            statusText.textContent = "Camera stopped.";
        });

        function startScanning(deviceId) {
            codeReader.decodeFromVideoDevice(deviceId, 'video', (result, err) => {
                if (result) {
                    console.log(result);
                    searchInput.value = result.text;
                    scannerModal.hide();
                    
                    // Create a visual flash effect on the input
                    searchInput.style.backgroundColor = '#e8f5e9';
                    setTimeout(() => {
                        searchInput.style.backgroundColor = '';
                        // Auto submit
                        searchInput.form.submit();
                    }, 500);
                }
                if (err && !(err instanceof ZXing.NotFoundException)) {
                    // console.error(err); // Ignore frequent scan errors
                }
            });
        }
    });
</script>
@endpush
