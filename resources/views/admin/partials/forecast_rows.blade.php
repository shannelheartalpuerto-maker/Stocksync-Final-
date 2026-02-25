@foreach($productForecasts as $forecast)
    <tr>
        <td class="ps-4">
            <div class="fw-bold text-dark">{{ $forecast['name'] }}</div>
            <small class="text-muted">{{ $forecast['category_name'] }}</small>
        </td>
        <td class="text-center">
            <span class="badge bg-light text-dark border">{{ $forecast['stock'] }}</span>
        </td>
        <td class="text-center">
            <div class="{{ $forecast['velocity_class'] }} fw-bold">
                <i class="fa-solid {{ $forecast['velocity_icon'] }} me-1"></i>
                {{ $forecast['velocity_status'] }}
            </div>
            <small class="text-muted" style="font-size: 0.75rem;">
                Avg: {{ number_format($forecast['avg_daily_sales'], 2) }}/day
            </small>
        </td>
        <td class="text-center">
            <span class="badge {{ $forecast['action_class'] == 'text-danger' ? 'bg-danger' : 'bg-success' }} bg-opacity-10 {{ $forecast['action_class'] }}">
                <i class="fa-solid {{ $forecast['action_icon'] }} me-1"></i>
                {{ $forecast['stock_action'] }}
                @if($forecast['action_qty'] > 0)
                    (+{{ $forecast['action_qty'] }})
                @endif
            </span>
        </td>
        <td class="text-center">
            <div class="d-inline-flex gap-2">
                <span class="badge bg-warning bg-opacity-10 text-warning" title="Suggested Low Stock">{{ $forecast['suggested_low_threshold'] }}</span>
                <span class="badge bg-success bg-opacity-10 text-success" title="Suggested Good Stock">{{ $forecast['suggested_good_stock'] }}</span>
                <span class="badge bg-danger bg-opacity-10 text-danger" title="Suggested Over Stock">{{ $forecast['suggested_overstock_threshold'] }}</span>
            </div>
        </td>
    </tr>
@endforeach
