@extends('layouts.app')

@section('content')
<div class="staff-container animate-fade-up">
    <!-- Notifications -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
        <div id="liveToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    Error message here.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="inv-topbar mb-4">
        <div class="inv-topbar-inner">
            <div class="inv-title-wrap">
                <span class="inv-title-icon"><i class="fa-solid fa-cash-register"></i></span>
                <h5 class="inv-title-text">POS Terminal</h5>
            </div>
            <div class="inv-header-actions">
                <span class="inv-head-pill"><i class="fa-solid fa-box"></i>{{ number_format($products->count()) }} Products</span>
                <span class="inv-head-pill"><i class="fa-solid fa-tags"></i>{{ isset($categories) ? number_format($categories->count()) : '0' }} Categories</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Product List -->
        <div class="col-lg-8">
            <div class="content-card d-flex flex-column" style="height: calc(100vh - 160px); min-height: 600px;">
                <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 flex-shrink-0">
                    <h5 class="card-title-custom mb-0"><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Product Catalog</h5>
                    <div class="d-flex gap-2 flex-wrap flex-grow-1 justify-content-end">
                        <select id="categoryFilter" class="form-select border-0 bg-light shadow-none" style="width: 160px;">
                            <option value="">All Categories</option>
                            @isset($categories)
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                        <select id="brandFilter" class="form-select border-0 bg-light shadow-none" style="width: 160px;">
                            <option value="">All Brands</option>
                            @isset($brands)
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                        <div class="input-group shadow-none" style="width: 220px;">
                            <span class="input-group-text bg-light border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" id="searchProduct" class="form-control border-0 bg-light shadow-none" placeholder="Search product..." autofocus>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom flex-grow-1 overflow-auto p-4 pt-2">
                    <div class="row g-3" id="productsContainer">
                        @foreach($products as $product)
                            @php
                                $statusColor = 'success';
                                $statusText = 'In Stock';
                                if ($product->quantity <= 0) {
                                    $statusColor = 'danger';
                                    $statusText = 'Out of Stock';
                                } elseif ($product->quantity < $product->low_stock_threshold) {
                                    $statusColor = 'warning';
                                    $statusText = 'Low Stock';
                                }
                            @endphp
                        <div class="col-md-4 col-xl-3 product-item" 
                             data-id="{{ $product->id }}"
                             data-name="{{ strtolower($product->name) }}" 
                             data-code="{{ $product->code }}" 
                             data-category-id="{{ $product->category_id }}" 
                             data-brand-id="{{ $product->brand_id }}"
                             data-stock="{{ $product->quantity }}">
                            <div id="product-card-{{ $product->id }}" class="pos-product-card {{ $product->quantity == 0 ? 'out-of-stock' : '' }}" 
                                 onclick="{{ $product->quantity > 0 ? 'addToCart('.$product->id.', \''.addslashes($product->name).'\', '.$product->price.', '.$product->quantity.')' : 'showError(\'Out of stock!\')' }}">
                                <div class="pos-product-img">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="no-img"><i class="fa-solid fa-box opacity-25"></i></div>
                                    @endif
                                    <div class="stock-pill" id="stock-display-{{ $product->id }}">{{ $product->quantity }}</div>
                                </div>
                                <div class="pos-product-info">
                                    <h6 class="name text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
                                    <div class="price">₱{{ number_format($product->price, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Section -->
        <div class="col-lg-4">
            <div class="content-card d-flex flex-column" style="height: calc(100vh - 160px); min-height: 600px;">
                <div class="card-header-custom py-3 border-bottom-0 flex-shrink-0">
                    <h5 class="card-title-custom mb-0"><i class="fa-solid fa-shopping-cart me-2 text-primary"></i>Current Order</h5>
                </div>
                
                <div class="cart-items-wrapper flex-grow-1 overflow-auto px-3" style="min-height: 150px;">
                    <div id="cartTableBody" class="cart-list">
                        <!-- Cart items will be rendered here -->
                    </div>
                    <div id="emptyCartMessage" class="text-center py-5 text-muted opacity-50">
                        <i class="fa-solid fa-cart-shopping fa-3x mb-3"></i>
                        <p class="fw-bold">No items added yet</p>
                    </div>
                </div>

                <div class="cart-footer p-3 bg-white border-top shadow-sm flex-shrink-0">
                    <div class="summary-row mb-1">
                        <span class="label small">Subtotal</span>
                        <span class="value small" id="subtotalAmount">₱0.00</span>
                    </div>
                    <div class="summary-row mb-3">
                        <span class="label fw-bold text-dark">Total</span>
                        <span class="value fs-4 fw-bold text-primary" id="totalAmount">₱0.00</span>
                    </div>
                    
                    <div class="payment-box mb-3 p-2 bg-light rounded-3">
                        <div class="d-flex align-items-center mb-2">
                            <label class="small fw-bold text-muted text-uppercase mb-0 flex-grow-1" style="font-size: 0.7rem;">Cash Received</label>
                            <div class="input-group input-group-sm" style="width: 130px;">
                                <span class="input-group-text bg-white border-end-0 pe-1">₱</span>
                                <input type="number" id="paymentReceived" class="form-control border-start-0 ps-1 fw-bold text-end shadow-none" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="small fw-bold text-muted text-uppercase flex-grow-1" style="font-size: 0.7rem;">Change</span>
                            <span class="fw-bold text-dark h6 mb-0" id="changeAmount">₱0.00</span>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" onclick="processSale()">
                        <i class="fa-solid fa-check-circle"></i> COMPLETE SALE
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="saleSuccessModal" data-bs-backdrop="false" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-body p-5 text-center bg-white position-relative">
        <div class="mb-4">
            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 90px; height: 90px;">
                <i class="fa-solid fa-circle-check fa-4x text-success animate-pop"></i>
            </div>
        </div>
        <h3 class="fw-bold text-dark mb-1">Order Confirmed!</h3>
        <p class="text-secondary small mb-4">Transaction #<span id="successTrxId">0000</span></p>
        
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="small text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Paid</div>
                    <div class="h5 fw-bold text-dark mb-0" id="successTotal">₱0.00</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-light rounded-3 text-center border border-success border-opacity-25">
                    <div class="small text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Change</div>
                    <div class="h5 fw-bold text-success mb-0" id="successChange">₱0.00</div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" id="printReceiptBtn" class="btn btn-outline-secondary btn-lg flex-grow-1 rounded-3 py-2 small fw-bold">
                <i class="fa-solid fa-print me-1"></i> Print Receipt
            </button>
            <button type="button" id="newOrderBtn" data-bs-dismiss="modal" class="btn btn-primary btn-lg flex-grow-1 rounded-3 py-2 small fw-bold">
                <i class="fa-solid fa-plus me-1"></i> New Order
            </button>
        </div>
      </div>
    </div>
  </div>
</div>

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
    :root {
        --pos-card-bg: #fff;
        --pos-primary: #4f46e5;
        --pos-border: #eef2ff;
        --pos-text: #1e293b;
        --pos-muted: #64748b;
    }

    .pos-product-card {
        background: var(--pos-card-bg);
        border: 1px solid var(--pos-border);
        border-radius: 10px;
        padding: 8px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }

    .pos-product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        border-color: var(--pos-primary);
    }

    .pos-product-card.out-of-stock {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .pos-product-card.out-of-stock:hover {
        transform: none;
        box-shadow: none;
        border-color: var(--pos-border);
    }

    .pos-product-img {
        width: 100%;
        aspect-ratio: 1;
        background: #f8fafc;
        border-radius: 6px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .pos-product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pos-product-img .no-img {
        font-size: 1.5rem;
        color: var(--pos-muted);
    }

    .stock-pill {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(255, 255, 255, 0.9);
        color: var(--pos-text);
        font-size: 0.65rem;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .pos-product-info .name {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 2px;
        color: var(--pos-text);
    }

    .pos-product-info .price {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--pos-primary);
    }

    .cart-list .cart-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 4px;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .cart-list .cart-item:hover {
        background: #f8fafc;
    }

    .cart-item-info {
        flex-grow: 1;
        min-width: 0;
    }

    .cart-item-info .name {
        font-size: 0.75rem;
        font-weight: 700;
        margin-bottom: 0;
        color: var(--pos-text);
    }

    .cart-item-info .price {
        font-size: 0.7rem;
        color: var(--pos-muted);
    }

    .cart-qty-controls {
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border-radius: 20px;
        padding: 2px;
    }

    .cart-qty-controls .btn {
        width: 22px;
        height: 22px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        border-radius: 50%;
        background: white;
        border: none;
        color: var(--pos-text);
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .cart-qty-controls .btn:hover {
        background: var(--pos-primary);
        color: white;
    }

    .cart-qty-controls .qty {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0 8px;
        min-width: 24px;
        text-align: center;
    }

    .cart-item-subtotal {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--pos-text);
        min-width: 65px;
        text-align: right;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .summary-row .label {
        color: var(--pos-muted);
        font-size: 0.85rem;
        font-weight: 600;
    }

    .summary-row .value {
        color: var(--pos-text);
        font-weight: 700;
    }

    .animate-pop {
        animation: pop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    /* Keep success modal above backdrop on all devices */
    .modal-backdrop.show {
        z-index: 2000 !important;
        opacity: 0 !important;
        background: transparent !important;
    }

    #saleSuccessModal {
        z-index: 2100 !important;
    }

    #saleSuccessModal .modal-dialog {
        position: relative !important;
        z-index: 2110 !important;
        pointer-events: auto !important;
    }

    #saleSuccessModal .modal-content {
        pointer-events: auto !important;
        opacity: 1 !important;
        filter: none !important;
    }

    @keyframes pop {
        0% { transform: scale(0.5); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Hide scrollbar for cleaner look but keep scroll functional */
    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>

<script>
    let cart = [];

    function cleanupModalArtifacts() {
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    function normalizeSuccessBackdrop() {
        const saleModal = document.getElementById('saleSuccessModal');
        const backdrops = document.querySelectorAll('.modal-backdrop');

        if (!saleModal || !saleModal.classList.contains('show')) return;

        // Success modal uses no backdrop. Remove any stray backdrop immediately.
        backdrops.forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
    
    function showError(message) {
        if (typeof bootstrap !== 'undefined') {
            document.getElementById('toastMessage').innerText = message;
            const toastEl = document.getElementById('liveToast');
            const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
            toast.show();
        } else {
            alert(message);
        }
    }

    function filterProducts() {
        let term = document.getElementById('searchProduct').value.toLowerCase();
        let catId = document.getElementById('categoryFilter').value;
        let brandId = document.getElementById('brandFilter').value;
        
        document.querySelectorAll('.product-item').forEach(item => {
            let matchesSearch = item.getAttribute('data-name').includes(term) || item.getAttribute('data-code').includes(term);
            let matchesCat = !catId || item.getAttribute('data-category-id') === catId;
            let matchesBrand = !brandId || item.getAttribute('data-brand-id') === brandId;
            item.style.display = (matchesSearch && matchesCat && matchesBrand) ? 'block' : 'none';
        });
    }

    document.getElementById('searchProduct').addEventListener('input', filterProducts);
    document.getElementById('categoryFilter').addEventListener('change', filterProducts);
    document.getElementById('brandFilter').addEventListener('change', filterProducts);

    document.addEventListener('DOMContentLoaded', function() {
        // Clear any ghost backdrop left from previous navigation/session.
        if (!document.querySelector('.modal.show')) {
            cleanupModalArtifacts();
        }

        const saleSuccessModalEl = document.getElementById('saleSuccessModal');
        const saleSuccessModal = bootstrap.Modal.getOrCreateInstance(saleSuccessModalEl);
        const printBtn = document.getElementById('printReceiptBtn');
        const newOrderBtn = document.getElementById('newOrderBtn');

        saleSuccessModalEl.addEventListener('hidden.bs.modal', function() {
            // Delay avoids racing with bootstrap hide transition.
            setTimeout(cleanupModalArtifacts, 20);
        });

        saleSuccessModalEl.addEventListener('shown.bs.modal', function() {
            normalizeSuccessBackdrop();
        });

        if (printBtn) {
            printBtn.addEventListener('click', function() {
                window.print();
            });
        }

        if (newOrderBtn) {
            newOrderBtn.addEventListener('click', function() {
                saleSuccessModal.hide();
                cart = [];
                document.getElementById('paymentReceived').value = '';
                renderCart();
                cleanupModalArtifacts();
            });
        }
    });

    function addToCart(id, name, price, maxQty) {
        let item = cart.find(i => i.id === id);
        if (item) {
            if (item.quantity >= maxQty) return showError('Out of stock!');
            item.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1, maxQty });
        }
        renderCart();
    }

    function updateQuantity(id, change) {
        let item = cart.find(i => i.id === id);
        if (!item) return;
        let newQty = item.quantity + change;
        if (newQty > item.maxQty) return showError('Max stock reached');
        if (newQty <= 0) cart = cart.filter(i => i.id !== id);
        else item.quantity = newQty;
        renderCart();
    }

    function updateCatalogStock() {
        // Reset all displays based on original stock from data attributes
        document.querySelectorAll('.product-item').forEach(item => {
            let id = item.getAttribute('data-id');
            let originalStock = parseInt(item.getAttribute('data-stock'));
            let display = document.getElementById('stock-display-' + id);
            let card = document.getElementById('product-card-' + id);
            
            if (display) {
                // Find if item is in cart to subtract
                let cartItem = cart.find(i => i.id == id);
                let currentStock = originalStock - (cartItem ? cartItem.quantity : 0);
                
                display.innerText = currentStock;
                
                // Update styling based on current stock
                if (currentStock <= 0) {
                    card.classList.add('out-of-stock');
                    display.classList.add('bg-danger', 'text-white');
                } else {
                    card.classList.remove('out-of-stock');
                    display.classList.remove('bg-danger', 'text-white');
                }
            }
        });
    }

    function renderCart() {
        const container = document.getElementById('cartTableBody');
        const emptyMsg = document.getElementById('emptyCartMessage');
        container.innerHTML = '';
        
        let total = 0;
        if (cart.length === 0) {
            emptyMsg.style.display = 'block';
        } else {
            emptyMsg.style.display = 'none';
            cart.forEach(item => {
                let subtotal = item.price * item.quantity;
                total += subtotal;
                container.innerHTML += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="name text-truncate">${item.name}</div>
                            <div class="price">₱${item.price.toFixed(2)}</div>
                        </div>
                        <div class="cart-qty-controls">
                            <button class="btn" onclick="updateQuantity(${item.id}, -1)"><i class="fa-solid fa-minus"></i></button>
                            <span class="qty">${item.quantity}</span>
                            <button class="btn" onclick="updateQuantity(${item.id}, 1)"><i class="fa-solid fa-plus"></i></button>
                        </div>
                        <div class="cart-item-subtotal">₱${subtotal.toFixed(2)}</div>
                        <button class="btn btn-link text-danger p-0 border-0" onclick="removeFromCart(${item.id})">
                            <i class="fa-solid fa-trash-can small opacity-50"></i>
                        </button>
                    </div>
                `;
            });
        }
        document.getElementById('subtotalAmount').innerText = '₱' + total.toFixed(2);
        document.getElementById('totalAmount').innerText = '₱' + total.toFixed(2);
        calculateChange();
        updateCatalogStock();
    }

    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function calculateChange() {
        let totalText = document.getElementById('totalAmount').innerText.replace('₱', '').replace(/,/g, '');
        let total = parseFloat(totalText) || 0;
        let received = parseFloat(document.getElementById('paymentReceived').value) || 0;
        let change = Math.max(0, received - total);
        document.getElementById('changeAmount').innerText = '₱' + change.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    document.getElementById('paymentReceived').addEventListener('input', calculateChange);

    function processSale() {
        if (cart.length === 0) return showError('Cart is empty!');
        let totalText = document.getElementById('totalAmount').innerText.replace('₱', '').replace(/,/g, '');
        let total = parseFloat(totalText) || 0;
        let received = parseFloat(document.getElementById('paymentReceived').value) || 0;
        
        if (received < total) return showError(`Insufficient payment. Need ₱${(total - received).toFixed(2)} more.`);

        $.ajax({
            url: '{{ route("staff.pos.process") }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', cart, payment_received: received },
            success: function(data) {
                if (data.success) {
                    document.getElementById('successTrxId').innerText = data.transaction_number;
                    document.getElementById('successTotal').innerText = '₱' + parseFloat(data.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2});
                    document.getElementById('successChange').innerText = '₱' + parseFloat(data.change_returned).toLocaleString(undefined, {minimumFractionDigits: 2});
                    cleanupModalArtifacts();
                    const saleSuccessModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('saleSuccessModal'));
                    saleSuccessModal.show();
                    setTimeout(normalizeSuccessBackdrop, 20);
                } else {
                    showError(data.error);
                }
            },
            error: function() { showError('Transaction failed.'); }
        });
    }
</script>
@endsection

