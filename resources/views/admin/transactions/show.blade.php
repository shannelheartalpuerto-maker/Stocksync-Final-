@extends('layouts.app')

@section('content')
<div class="container fade-in-up">
    <div class="mb-4">
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to Transactions
        </a>
        <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-center rounded-3 px-4 shadow-sm border-0">
            <div>
                <h2 class="fw-bold text-dark mb-1">Transaction Details</h2>
                <p class="text-muted mb-0">Reference ID: #{{ $transaction->id }}</p>
            </div>
            <div class="text-end mt-3 mt-md-0">
                <h3 class="fw-bold text-success mb-0">₱{{ number_format($transaction->total_amount, 2) }}</h3>
                <small class="text-muted">{{ $transaction->created_at->format('M d, Y h:i A') }}</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-basket-shopping me-2 text-primary"></i>Purchased Items</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->items as $item)
                            <tr>
                                <td>
                                    <span class="fw-bold d-block">{{ $item->product->name }}</span>
                                    <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">₱{{ number_format($item->product->price, 2) }}</td>
                                <td class="text-end fw-bold">₱{{ number_format($item->quantity * $item->product->price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total Amount</td>
                                <td class="text-end fw-bold fs-5 text-success">₱{{ number_format($transaction->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-circle-info me-2 text-info"></i>Transaction Info</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Date</span>
                            <span class="fw-bold">{{ $transaction->created_at->format('M d, Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Time</span>
                            <span class="fw-bold">{{ $transaction->created_at->format('h:i A') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Processed By</span>
                            <span class="badge bg-light text-dark border">{{ $transaction->user->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Payment Method</span>
                            <span class="fw-bold">Cash</span> <!-- Assuming Cash for now as per POS -->
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-4">
                    <i class="fa-solid fa-print fa-3x text-muted mb-3"></i>
                    <h5 class="fw-bold">Print Receipt</h5>
                    <p class="text-muted small mb-3">Generate a PDF receipt for this transaction.</p>
                    <button class="btn btn-outline-primary w-100" onclick="window.print()">
                        <i class="fa-solid fa-print me-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
