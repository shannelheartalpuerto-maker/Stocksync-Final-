@foreach($admins as $admin)
<tr>
    <td class="ps-3 fw-bold text-dark">{{ $admin->name }}</td>
    <td>{{ $admin->email }}</td>
    <td>
        <span class="badge bg-{{ $admin->status == 'active' ? 'success' : 'danger' }} rounded-pill">
            {{ ucfirst($admin->status) }}
        </span>
    </td>
    <td class="ps-3">
        <div class="dropdown dropend">
            <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport">
                <i class="fa-solid fa-gear"></i>
            </button>
            <div class="dropdown-menu p-2" style="min-width: auto;">
                <div class="d-flex gap-2">
                    @if($admin->status == 'active')
                        <button type="button" class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#ajax-suspendAdminModal{{ $admin->id }}">
                            <i class="fa-solid fa-ban me-1"></i>Suspend
                        </button>
                    @else
                        <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="active">
                            <button type="submit" class="btn btn-success btn-sm text-white">
                                <i class="fa-solid fa-check me-1"></i>Activate
                            </button>
                        </form>
                    @endif
                    
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#ajax-deleteAdminModal{{ $admin->id }}">
                        <i class="fa-solid fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </td>
    </tr>
@endforeach
