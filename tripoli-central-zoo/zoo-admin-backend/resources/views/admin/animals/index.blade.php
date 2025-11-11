@extends('layouts.admin')

@section('title', 'Animals')
@section('page-title', 'Animals Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Animals</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Animals List</h3>
            <div class="card-tools">
                <a href="{{ route('admin.animals.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Animal
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button class="btn btn-sm btn-danger" id="bulkDelete" disabled>
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
                <button class="btn btn-sm btn-success" id="bulkActivate" disabled>
                    <i class="fas fa-check"></i> Activate Selected
                </button>
            </div>
            
            <table class="table table-bordered table-striped" id="animalsTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Species</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($animals as $animal)
                        <tr>
                            <td><input type="checkbox" class="animal-checkbox" value="{{ $animal->id }}"></td>
                            <td>
                                @if($animal->image_url)
                                    <img src="{{ $animal->image_url }}" alt="{{ $animal->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <span class="badge badge-secondary">No Image</span>
                                @endif
                            </td>
                            <td>{{ $animal->name }}</td>
                            <td>{{ $animal->species }}</td>
                            <td>{{ $animal->category ? $animal->category->name : 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $animal->status === 'active' ? 'success' : ($animal->status === 'inactive' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($animal->status) }}
                                </span>
                            </td>
                            <td>
                                @if($animal->featured)
                                    <i class="fas fa-star text-warning"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.animals.edit', $animal) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.animals.destroy', $animal) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No animals found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $animals->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.animal-checkbox').prop('checked', this.checked);
        toggleBulkButtons();
    });

    // Individual checkbox
    $('.animal-checkbox').on('change', function() {
        toggleBulkButtons();
    });

    function toggleBulkButtons() {
        const checked = $('.animal-checkbox:checked').length > 0;
        $('#bulkDelete, #bulkActivate').prop('disabled', !checked);
    }

    // Bulk delete
    $('#bulkDelete').on('click', function() {
        const ids = $('.animal-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (confirm('Are you sure you want to delete ' + ids.length + ' animals?')) {
            $.ajax({
                url: '{{ route("admin.animals.bulk-delete") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: ids
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    });

    // Bulk activate
    $('#bulkActivate').on('click', function() {
        const ids = $('.animal-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        $.ajax({
            url: '{{ route("admin.animals.bulk-update-status") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ids: ids,
                status: 'active'
            },
            success: function(response) {
                location.reload();
            }
        });
    });
});
</script>
@endpush
