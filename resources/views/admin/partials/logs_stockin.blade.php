<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>User</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockIns as $log)
            <tr>
                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <div class="fw-bold">{{ $log->product->name ?? 'Deleted Product' }}</div>
                    <small class="text-muted">{{ $log->product->code ?? '-' }}</small>
                </td>
                <td><span class="badge bg-success">+{{ $log->quantity }}</span></td>
                <td>{{ $log->user->name ?? 'System' }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#stockInDetailsModal{{ $log->id }}">
                        <i class="fa-solid fa-eye me-1"></i>View
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">No stock in records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $stockIns->appends(['returned_page' => $returnedStocks->currentPage(), 'out_page' => $stockOuts->currentPage(), 'damaged_page' => $damagedStocks->currentPage(), 'period' => $period])->links() }}

@foreach($stockIns as $log)
<div class="modal fade" id="stockInDetailsModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-arrow-down me-2 text-success"></i>Stock In Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Date & Time</label>
                        <p class="mb-0">{{ $log->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Added By</label>
                        <p class="mb-0">{{ $log->user->name ?? 'System' }}</p>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Product Details</label>
                        <p class="mb-0 fw-bold">{{ $log->product->name ?? 'Deleted Product' }}</p>
                        <small class="text-muted">{{ $log->product->code ?? '-' }}</small>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Quantity Added</label>
                        <div><span class="badge bg-success">+{{ $log->quantity }}</span></div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase mb-2">Notes</label>
                        <div class="p-3 bg-white border rounded text-break">
                            {{ $log->notes }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
