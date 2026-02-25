<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
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
                <td><span class="fw-bold text-dark">#{{ substr($trx->transaction_number, -6) }}</span></td>
                <td>{{ $trx->created_at->format('Y-m-d H:i') }}</td>
                <td>₱{{ number_format($trx->total_amount, 2) }}</td>
                <td>
                    <small class="text-muted">
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
                <td colspan="6" class="text-center text-muted py-4">No transactions found.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="mt-3">
    {{ $transactions->links() }}
</div>

<!-- Return Modals -->
@push('modals')
@foreach($transactions as $trx)
@if($trx->status !== 'returned')
<div class="modal fade" id="returnModal{{ $trx->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('staff.transactions.return', $trx->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-rotate-left me-2"></i>Process Return</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="mb-3 text-danger">
                            <i class="fa-solid fa-circle-exclamation fa-4x opacity-75"></i>
                        </div>
                        <h5 class="fw-bold">Confirm Return</h5>
                        <p class="text-muted">Are you sure you want to process a return for transaction <span class="fw-bold text-dark">#{{ substr($trx->transaction_number, -6) }}</span>?</p>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Reason for Return <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Please describe why this item is being returned..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold"><i class="fa-solid fa-check me-2"></i>Confirm Return</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endpush
