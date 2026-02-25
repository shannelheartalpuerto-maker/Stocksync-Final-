@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="text-center mb-5">
        <h2 class="fw-bold display-5 text-primary mb-2"><i class="fa-solid fa-file-invoice-dollar me-3"></i>My Transactions</h2>
        <p class="text-muted lead">View your sales history and manage returns.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-list me-2"></i>Transaction History</h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead>
                                <tr>
                                    <th>TRX Number</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Items</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $trx)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $trx->transaction_number }}</span>
                                    </td>
                                    <td>{{ $trx->created_at->format('Y-m-d H:i') }}</td>
                                    <td>₱{{ number_format($trx->total_amount, 2) }}</td>
                                    <td>
                                        <small>
                                            @foreach($trx->items as $item)
                                                {{ $item->product->name ?? 'Unknown' }} (x{{ $item->quantity }})<br>
                                            @endforeach
                                        </small>
                                    </td>
                                    <td>
                                        @if($trx->status === 'returned')
                                            <span class="badge bg-danger">RETURNED</span>
                                        @else
                                            <span class="badge bg-success">COMPLETED</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($trx->status !== 'returned')
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#returnModal{{ $trx->id }}">
                                                <i class="fa-solid fa-rotate-left"></i> Return
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled>Returned</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                                @if($transactions->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">No transactions found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Return Modals -->
@foreach($transactions as $trx)
@if($trx->status !== 'returned')
<div class="modal fade" id="returnModal{{ $trx->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('staff.transactions.return', $trx->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="fa-solid fa-rotate-left me-2"></i>Process Return</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> You are about to return Transaction <strong>{{ $trx->transaction_number }}</strong>.
                        <br><br>
                        This will:
                        <ul>
                            <li>Mark the transaction as RETURNED.</li>
                            <li>Restore <strong>{{ $trx->items->sum('quantity') }} items</strong> to inventory.</li>
                        </ul>
                        Are you sure you want to proceed?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Return</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection
