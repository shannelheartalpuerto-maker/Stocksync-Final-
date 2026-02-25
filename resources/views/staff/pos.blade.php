@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin-dashboard-design.css') }}?v={{ time() }}" rel="stylesheet">
<div class="container-fluid px-4 admin-dashboard-container animate-fade-up">
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

    <!-- Mobile Cart Summary (Visible only on mobile) -->
    <div class="fixed-bottom bg-white border-top shadow p-3 d-lg-none">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="small text-muted d-block">Total</span>
                <span class="fs-4 fw-bold text-primary" id="mobileTotalAmount">₱0.00</span>
            </div>
            <button class="btn btn-primary fw-bold px-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas">
                <i class="fa-solid fa-cart-shopping me-2"></i> View Cart <span class="badge bg-white text-primary ms-1" id="mobileCartCount">0</span>
            </button>
        </div>
    </div>

    <!-- Cart Offcanvas for Mobile -->
    <div class="offcanvas offcanvas-bottom h-75" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
        <div class="offcanvas-header bg-light border-bottom">
            <h5 class="offcanvas-title fw-bold" id="cartOffcanvasLabel"><i class="fa-solid fa-cart-shopping me-2"></i>Current Sale</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            <div class="table-responsive flex-grow-1 p-3">
                <table class="table table-striped align-middle mb-0">
                    <thead class="bg-white sticky-top">
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody id="mobileCartTableBody">
                        <!-- Mobile Cart Items -->
                    </tbody>
                </table>
                 <div id="mobileEmptyCartMessage" class="text-center py-5 text-muted" style="display: none;">
                    <i class="fa-solid fa-cart-arrow-down fs-1 mb-2 opacity-25"></i>
                    <p>Cart is empty</p>
                </div>
            </div>
            <div class="p-3 bg-light border-top mt-auto">
                 <div class="d-flex justify-content-between mb-2">
                    <span class="fs-5">Total:</span>
                    <span class="fs-4 fw-bold text-primary" id="mobileOffcanvasTotal">₱0.00</span>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase text-muted">Payment Received</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white">₱</span>
                        <input type="number" id="mobilePaymentReceived" class="form-control" step="0.01" placeholder="0.00">
                    </div>
                </div>
                 <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="small fw-bold text-muted">CHANGE:</span>
                    <span class="fs-5 fw-bold" id="mobileChangeAmount">₱0.00</span>
                </div>
                <button class="btn btn-success w-100 btn-lg fw-bold py-3 shadow-sm" onclick="processSale(true)">
                    <i class="fa-solid fa-check-circle me-2"></i>COMPLETE SALE
                </button>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="dashboard-header text-center mb-4">
        <h2 class="dashboard-title"><i class="fa-solid fa-cash-register me-3"></i>Point of Sale</h2>
        <p class="dashboard-subtitle">Process sales and manage transactions efficiently.</p>
    </div>

    <div class="row g-4 mb-5 pb-5 mb-lg-0 pb-lg-0">
        <!-- Product List -->
        <div class="col-lg-8 mb-4">
            <div class="content-card h-100 shadow-sm">
                <div class="card-header-custom py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Products</h5>
                        <div class="d-flex flex-column flex-md-row gap-2 flex-grow-1 justify-content-end">
                            <select id="categoryFilter" class="form-select w-100 w-md-auto">
                                <option value="">All Categories</option>
                                @isset($categories)
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <select id="brandFilter" class="form-select w-100 w-md-auto">
                                <option value="">All Brands</option>
                                @isset($brands)
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <input type="text" id="searchProduct" class="form-control w-100 w-md-auto" placeholder="Search..." autofocus>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom p-3" style="height: 75vh; overflow-y: auto;">
                    <div class="row g-3" id="productsContainer">
                        @foreach($products as $product)
                            @php
                                $statusColor = 'success';
                                $statusText = 'Good Stock';
                                if ($product->quantity <= 0) {
                                    $statusColor = 'danger';
                                    $statusText = 'Out of Stock';
                                } elseif ($product->quantity < $product->low_stock_threshold) {
                                    $statusColor = 'danger';
                                    $statusText = 'Low Stock';
                                } elseif ($product->quantity > $product->overstock_threshold) {
                                    $statusColor = 'warning text-dark';
                                    $statusText = 'Overstock';
                                }
                            @endphp
                        <div class="col-6 col-md-4 col-lg-3 product-item" data-name="{{ strtolower($product->name) }}" data-code="{{ $product->code }}" data-category-id="{{ $product->category_id }}" data-category-name="{{ $product->category ? strtolower($product->category->name) : '' }}" data-brand-id="{{ $product->brand_id }}" data-brand-name="{{ $product->brand ? strtolower($product->brand->name) : '' }}">
                                <div class="card h-100 product-card border-0 shadow-sm {{ $product->quantity == 0 ? 'opacity-50' : 'cursor-pointer' }}" 
                                 onclick="{{ $product->quantity > 0 ? 'addToCart('.$product->id.', \''.addslashes($product->name).'\', '.$product->price.', '.$product->quantity.')' : 'showError(\'Out of stock!\')' }}">
                                <div class="card-body text-center p-3 d-flex flex-column">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 60px;">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="img-fluid rounded" 
                                             style="max-height: 60px; max-width: 100%;"
                                             onerror="this.parentNode.innerHTML='<div class=\'avatar avatar-md bg-light text-primary rounded-circle mx-auto d-flex align-items-center justify-content-center\' style=\'width: 48px; height: 48px;\'><span class=\'fw-bold\'>{{ substr($product->name, 0, 1) }}</span></div>'">
                                        @else
                                            <div class="avatar avatar-md bg-light text-primary rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <span class="fw-bold">{{ substr($product->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <h6 class="card-title fw-bold text-truncate mb-1" title="{{ $product->name }}">{{ $product->name }}</h6>
                                    <small class="text-muted d-block mb-2">{{ $product->code }}</small>
                                    
                                    <div class="mt-auto">
                                        <h5 class="text-primary fw-bold mb-2">₱{{ number_format($product->price, 2) }}</h5>
                                        <div class="d-flex justify-content-center gap-1">
                                            <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} border border-{{ $statusColor }}">
                                                {{ $statusText }}
                                            </span>
                                            <span class="badge bg-dark">{{ $product->quantity }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart/Checkout -->
        <div class="col-lg-4">
            <div class="content-card h-100 shadow-sm">
                <div class="card-header-custom py-3">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-cart-shopping me-2"></i>Current Sale</h5>
                </div>
                <div class="card-body-custom d-flex flex-column p-0">
                    <div class="table-responsive flex-grow-1" style="max-height: 45vh; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-3 small">Item</th>
                                    <th class="text-center small" style="width: 90px;">Qty</th>
                                    <th class="text-end pe-3 small">Total</th>
                                    <th style="width: 30px;"></th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody">
                                <!-- Cart Items go here -->
                            </tbody>
                        </table>
                        <div id="emptyCartMessage" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-cart-arrow-down fs-1 mb-2 opacity-25"></i>
                            <p>Cart is empty</p>
                        </div>
                    </div>
                    
                    <div class="p-3 bg-light border-top mt-auto">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Total:</span>
                            <span class="fs-5 fw-bold text-primary" id="totalAmount">₱0.00</span>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-uppercase text-muted mb-1" style="font-size: 0.7rem;">Payment Received</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">₱</span>
                                <input type="number" id="paymentReceived" class="form-control" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <span class="small fw-bold text-muted">CHANGE:</span>
                            <span class="fs-6 fw-bold" id="changeAmount">₱0.00</span>
                        </div>
                        <button class="btn btn-success w-100 fw-bold py-2 shadow-sm" onclick="processSale()">
                            <i class="fa-solid fa-check-circle me-2"></i>COMPLETE SALE
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="saleSuccessModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4 border-0 shadow-lg">
      <div class="mb-3">
        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
            <i class="fa-solid fa-check fa-3x text-success"></i>
        </div>
      </div>
      <h3 class="fw-bold text-success mb-2">Sale Completed!</h3>
      <p class="text-muted mb-4">The transaction has been recorded successfully.</p>
      
      <div class="bg-light p-3 rounded-3 mb-4 text-start">
        <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Transaction ID:</span>
            <span class="fw-bold" id="successTrxId">#TRX-0000</span>
        </div>
        <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Total Amount:</span>
            <span class="fw-bold" id="successTotal">₱0.00</span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-muted">Change:</span>
            <span class="fw-bold text-success" id="successChange">₱0.00</span>
        </div>
      </div>

      <div class="d-grid gap-2">
        <button type="button" class="btn btn-primary btn-lg fw-bold" onclick="location.reload()">
            <i class="fa-solid fa-plus me-2"></i>Start New Sale
        </button>
      </div>
    </div>
  </div>
</div>

<script>
    let cart = [];
    
    // Toast Notification
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

    let searchInput = document.getElementById('searchProduct');
    function filterProducts() {
        let term = (searchInput && searchInput.value ? searchInput.value.toLowerCase() : '');
        let selectedCategory = document.getElementById('categoryFilter') ? document.getElementById('categoryFilter').value : '';
        let items = document.querySelectorAll('.product-item');
        items.forEach(item => {
            let name = item.getAttribute('data-name') || '';
            let code = item.getAttribute('data-code') || '';
            let catId = item.getAttribute('data-category-id') || '';
            let brandId = item.getAttribute('data-brand-id') || '';
            let matchesSearch = name.includes(term) || code.includes(term);
            let matchesCategory = selectedCategory === '' || catId === selectedCategory;
            let selectedBrand = document.getElementById('brandFilter') ? document.getElementById('brandFilter').value : '';
            let matchesBrand = selectedBrand === '' || brandId === selectedBrand;
            item.style.display = (matchesSearch && matchesCategory && matchesBrand) ? 'block' : 'none';
        });
    }
    if(searchInput){
        searchInput.addEventListener('input', filterProducts);
    }
    if(document.getElementById('categoryFilter')){
        document.getElementById('categoryFilter').addEventListener('change', filterProducts);
    }
    if(document.getElementById('brandFilter')){
        document.getElementById('brandFilter').addEventListener('change', filterProducts);
    }

    function addToCart(id, name, price, maxQty) {
        let existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            if (existingItem.quantity >= maxQty) {
                showError('Cannot add more. Stock limit reached.');
                return;
            }
            existingItem.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1, maxQty });
        }
        
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }

    function updateQuantity(id, change) {
        let item = cart.find(item => item.id === id);
        if (!item) return;

        let newQty = item.quantity + change;
        
        if (newQty > item.maxQty) {
            showError('Stock limit reached.');
            return;
        }
        
        if (newQty <= 0) {
            removeFromCart(id);
        } else {
            item.quantity = newQty;
            renderCart();
        }
    }

    function renderCart() {
        const tbody = document.getElementById('cartTableBody');
        const mobileTbody = document.getElementById('mobileCartTableBody');
        const emptyMsg = document.getElementById('emptyCartMessage');
        const mobileEmptyMsg = document.getElementById('mobileEmptyCartMessage');
        
        tbody.innerHTML = '';
        if(mobileTbody) mobileTbody.innerHTML = '';
        
        let total = 0;
        let count = 0;

        if (cart.length === 0) {
            emptyMsg.style.display = 'block';
            if(mobileEmptyMsg) mobileEmptyMsg.style.display = 'block';
        } else {
            emptyMsg.style.display = 'none';
            if(mobileEmptyMsg) mobileEmptyMsg.style.display = 'none';

            cart.forEach(item => {
                let subtotal = item.price * item.quantity;
                total += subtotal;
                count += item.quantity;

                // Desktop Row
                let row = `
                    <tr>
                        <td class="ps-2 py-2">
                            <div class="fw-bold small text-truncate" style="max-width: 140px;" title="${item.name}">${item.name}</div>
                            <small class="text-muted" style="font-size: 0.75rem;">₱${item.price.toFixed(2)}</small>
                        </td>
                        <td class="text-center py-2">
                            <div class="input-group input-group-sm justify-content-center" style="width: 80px; margin: 0 auto;">
                                <button class="btn btn-outline-secondary px-2 py-0" onclick="updateQuantity(${item.id}, -1)">-</button>
                                <span class="input-group-text bg-white px-2 py-0 border-secondary border-opacity-25" style="min-width: 30px; justify-content: center;">${item.quantity}</span>
                                <button class="btn btn-outline-secondary px-2 py-0" onclick="updateQuantity(${item.id}, 1)">+</button>
                            </div>
                        </td>
                        <td class="text-end pe-2 py-2 fw-bold small">₱${subtotal.toFixed(2)}</td>
                        <td class="text-end pe-2 py-2">
                            <button class="btn btn-sm text-danger p-0" onclick="removeFromCart(${item.id})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
                
                // Mobile Row
                if(mobileTbody) {
                    let mobileRow = `
                        <tr>
                            <td>
                                <div class="fw-bold text-truncate" style="max-width: 120px;">${item.name}</div>
                                <small class="text-muted">₱${item.price.toFixed(2)}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary p-1" onclick="updateQuantity(${item.id}, -1)">-</button>
                                    <button class="btn btn-light disabled p-1 text-dark border-top border-bottom" style="width: 25px;">${item.quantity}</button>
                                    <button class="btn btn-outline-secondary p-1" onclick="updateQuantity(${item.id}, 1)">+</button>
                                </div>
                            </td>
                            <td class="text-end fw-bold">₱${subtotal.toFixed(2)}</td>
                        </tr>
                    `;
                    mobileTbody.innerHTML += mobileRow;
                }
            });
        }

        document.getElementById('totalAmount').innerText = '₱' + total.toFixed(2);
        if(document.getElementById('mobileTotalAmount')) document.getElementById('mobileTotalAmount').innerText = '₱' + total.toFixed(2);
        if(document.getElementById('mobileOffcanvasTotal')) document.getElementById('mobileOffcanvasTotal').innerText = '₱' + total.toFixed(2);
        
        document.getElementById('mobileCartCount').innerText = count;
        
        calculateChange();
    }

    function calculateChange() {
        let total = parseFloat(document.getElementById('totalAmount').innerText.replace('₱', '').replace(',', ''));
        let received = parseFloat(document.getElementById('paymentReceived').value) || 0;
        
        // Sync mobile/desktop inputs if needed, or just handle independently. 
        // For simplicity, let's sync them if one changes.
        
        let change = received - total;
        if (change < 0) change = 0;
        
        document.getElementById('changeAmount').innerText = '₱' + change.toFixed(2);
        if(document.getElementById('mobileChangeAmount')) document.getElementById('mobileChangeAmount').innerText = '₱' + change.toFixed(2);
    }

    document.getElementById('paymentReceived').addEventListener('input', function(e) {
        if(document.getElementById('mobilePaymentReceived')) document.getElementById('mobilePaymentReceived').value = e.target.value;
        calculateChange();
    });
    
    if(document.getElementById('mobilePaymentReceived')) {
        document.getElementById('mobilePaymentReceived').addEventListener('input', function(e) {
            document.getElementById('paymentReceived').value = e.target.value;
            // Also update the local received var for calculation
             let total = parseFloat(document.getElementById('totalAmount').innerText.replace('₱', '').replace(',', ''));
             let received = parseFloat(e.target.value) || 0;
             let change = received - total;
             if (change < 0) change = 0;
             document.getElementById('changeAmount').innerText = '₱' + change.toFixed(2);
             document.getElementById('mobileChangeAmount').innerText = '₱' + change.toFixed(2);
        });
    }

    function processSale(isMobile = false) {
        if (cart.length === 0) {
            showError('Cart is empty!');
            return;
        }

        let total = parseFloat(document.getElementById('totalAmount').innerText.replace('₱', '').replace(',', ''));
        let receivedInput = isMobile ? document.getElementById('mobilePaymentReceived') : document.getElementById('paymentReceived');
        let paymentReceived = parseFloat(receivedInput.value) || 0;

        if (paymentReceived < total) {
            let shortage = (total - paymentReceived).toFixed(2);
            showError(`Insufficient payment! You are short by ₱${shortage}.`);
            return;
        }

        // Send to server
        $.ajax({
            url: '{{ route("staff.pos.process") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                cart: cart,
                payment_received: paymentReceived
            },
            success: function(data) {
                if (data.success) {
                    // Populate and show success modal
                    document.getElementById('successTrxId').innerText = '#' + data.transaction_number;
                    document.getElementById('successTotal').innerText = '₱' + parseFloat(data.total_amount).toFixed(2);
                    document.getElementById('successChange').innerText = '₱' + parseFloat(data.change_returned).toFixed(2);
                    
                    if (typeof bootstrap !== 'undefined') {
                        const successModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('saleSuccessModal'));
                        successModal.show();
                    } else {
                        alert('Sale Completed! TRX: ' + data.transaction_number + '\nTotal: ₱' + parseFloat(data.total_amount).toFixed(2) + '\nChange: ₱' + parseFloat(data.change_returned).toFixed(2));
                        location.reload();
                    }
                    
                    // Clear cart locally so if they close modal without reload (though backdrop is static), it's clear
                    cart = [];
                    renderCart();
                    document.getElementById('paymentReceived').value = '';
                    if(document.getElementById('mobilePaymentReceived')) document.getElementById('mobilePaymentReceived').value = '';
                    calculateChange();
                } else {
                    alert('Error: ' + data.error);
                }
            },
            error: function(xhr) {
                 console.error(xhr.responseText);
                 let msg = 'Transaction failed.';
                 if(xhr.responseJSON && xhr.responseJSON.error) {
                     msg = xhr.responseJSON.error;
                 } else {
                     msg += ' Check console for details. (' + xhr.status + ')';
                 }
                 showError(msg);
            }
        });
    }
</script>
@endsection
