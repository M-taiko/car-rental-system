@props(['rental'])

@php
    // Check if rental exists and is not soft-deleted
    $rentalExists = $rental && (!method_exists($rental, 'trashed') || !$rental->trashed());
    $rentalId = $rentalExists ? $rental->id : 0;
    $status = $rentalExists ? $rental->status : 'deleted';
@endphp

<div class="btn-group">
    @if($rentalExists)
        <a href="{{ route('rentals.show', $rentalId) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
            <i class="fe fe-eye"></i>
        </a>
        
        @can('rental-edit')
        <a href="{{ route('rentals.edit', $rentalId) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="تعديل">
            <i class="fe fe-edit"></i>
        </a>
        @endcan
        
        @if($status === 'active' && auth()->user()->can('rental-return'))
        <form action="{{ route('rentals.return', $rentalId) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="إرجاع السيارة">
                <i class="fe fe-check-circle"></i>
            </button>
        </form>
        @endif
        
        @can('rental-delete')
        <form action="{{ route('rentals.destroy', $rentalId) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الإيجار؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger delete-rental" data-bs-toggle="tooltip" title="حذف">
                <i class="fe fe-trash-2"></i>
            </button>
        </form>
        @endcan
    @else
        <span class="text-muted" data-bs-toggle="tooltip" title="سجل محذوف">
            <i class="fe fe-alert-triangle"></i> محذوف
        </span>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            container: 'body',
            trigger: 'hover',
            placement: 'top',
            html: true
        });
    });
    
    // Handle delete button click
    document.querySelectorAll('.delete-rental').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('هل أنت متأكد من حذف هذا الإيجار؟')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    });
});
</script>
