<div class="list-group list-group-custom list-group-flush">
    @forelse($topSellingProducts as $item)
        <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold text-dark">{{ $item->name }}</div>
                    <small class="text-muted">{{ $item->code }}</small>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-dark">{{ $item->total_qty }} sold</div>
                    <small class="text-muted">₱{{ number_format($item->total_revenue, 2) }}</small>
                </div>
            </div>
        </div>
    @empty
        <div class="p-4 text-center text-muted">No sales found for this period</div>
    @endforelse
</div>