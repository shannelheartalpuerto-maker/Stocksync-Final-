<div class="list-group list-group-custom list-group-flush">
@forelse($recentTransactions as $transaction)
    <div class="list-group-item">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="me-3 rounded-circle p-2 bg-primary bg-opacity-10 text-primary">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">#{{ $transaction->id }}</div>
                    <small class="text-muted">{{ $transaction->user->name ?? 'System' }}</small>
                </div>
            </div>
            <div class="text-end">
                <div class="fw-bold text-success">₱{{ number_format($transaction->total_amount, 2) }}</div>
                <small class="text-muted">{{ $transaction->created_at->format('M d') }}</small>
            </div>
        </div>
    </div>
@empty
    <div class="p-5 text-center text-muted">
        <i class="fa-solid fa-receipt mb-3 fa-2x opacity-50"></i>
        <p class="mb-0">No transactions yet</p>
    </div>
@endforelse
</div>
