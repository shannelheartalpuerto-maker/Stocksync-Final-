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
            @forelse($returnedItems as $log)
            <tr>
                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <div class="fw-bold">{{ $log->product->name ?? 'Deleted Product' }}</div>
                    <small class="text-muted">{{ $log->product->code ?? '-' }}</small>
                </td>
                <td><span class="badge bg-success">+{{ $log->quantity }}</span></td>
                <td>{{ $log->user->name ?? 'System' }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#returnDetailsModal{{ $log->id }}">
                        <i class="fa-solid fa-eye me-1"></i>View
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">No returned items found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $returnedItems->appends(request()->query())->links() }}

<!-- Return Details Modals -->
@foreach($returnedItems as $log)
@php
    $noteParts = explode('|', $log->notes);
    $transactionRef = trim($noteParts[0]);
    $reasonText = isset($noteParts[1]) ? trim(str_replace('Reason:', '', $noteParts[1])) : 'No specific reason provided';
    
    // Handle old format or missing separator
    if (count($noteParts) === 1 && str_contains($log->notes, 'Return of Transaction')) {
        $transactionRef = $log->notes;
        $reasonText = 'No reason recorded for this transaction';
    } elseif (count($noteParts) === 1) {
         $transactionRef = 'Manual Entry';
         $reasonText = $log->notes;
    }
@endphp
<div class="modal fade" id="returnDetailsModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-rotate-left me-2 text-danger"></i>Return Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-light border mb-4">
                    <i class="fa-solid fa-receipt me-2 text-muted"></i><span class="fw-bold text-dark">{{ $transactionRef }}</span>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Date & Time</label>
                        <p class="mb-0">{{ $log->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Processed By</label>
                        <p class="mb-0">{{ $log->user->name ?? 'System' }}</p>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Product Details</label>
                        <p class="mb-0 fw-bold">{{ $log->product->name ?? 'Deleted Product' }}</p>
                        <small class="text-muted">{{ $log->product->code ?? '-' }}</small>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Quantity Returned</label>
                        <div><span class="badge bg-success text-white">+{{ $log->quantity }}</span></div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase mb-2">Reason for Return</label>
                        <div class="p-3 bg-white border rounded text-break">
                            {{ $reasonText }}
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

<script>
    (function() {
        var modalIds = [
            @foreach($returnedItems as $log)
                "returnDetailsModal{{ $log->id }}",
            @endforeach
        ];

        modalIds.forEach(function(id) {
            // Remove any existing zombie modals from body (from previous AJAX loads)
            var existing = document.querySelectorAll('body > #' + id);
            existing.forEach(function(el) { el.remove(); });

            // Move the new modal to body to avoid backdrop/z-index issues
            var modal = document.getElementById(id);
            if (modal) {
                document.body.appendChild(modal);
            }
        });
    })();
</script>
