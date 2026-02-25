@foreach($categories as $category)
<tr>
    <td class="ps-3 fw-bold text-dark">{{ $category->name }}</td>
    <td><span class="badge bg-secondary rounded-pill">{{ $category->products->count() }} Products</span></td>
    <td class="ps-3">
        <div class="dropdown dropend">
            <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport">
                <i class="fa-solid fa-gear"></i>
            </button>
            <div class="dropdown-menu p-2" style="min-width: auto;">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajax-editCategoryModal{{ $category->id }}">
                        <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#ajax-deleteCategoryModal{{ $category->id }}">
                        <i class="fa-solid fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </td>
    </tr>
@endforeach
