@extends('layouts.admin')

@section('title', 'Facilities')
@section('page-title', 'Facilities Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Facilities</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Facilities List</h3>
            <div class="card-tools">
                <a href="{{ route('admin.facilities.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Facility
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Accessible</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facilities as $facility)
                        <tr>
                            <td>
                                @if($facility->image_url)
                                    <img src="{{ $facility->image_url }}" alt="{{ $facility->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <span class="badge badge-secondary">No Image</span>
                                @endif
                            </td>
                            <td>{{ $facility->name }}</td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $facility->type)) }}</span>
                            </td>
                            <td>{{ $facility->category ? $facility->category->name : 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $facility->status === 'open' ? 'success' : ($facility->status === 'closed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($facility->status) }}
                                </span>
                            </td>
                            <td>
                                @if($facility->is_accessible)
                                    <i class="fas fa-wheelchair text-success"></i>
                                @else
                                    <i class="fas fa-times text-danger"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.facilities.edit', $facility) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.facilities.destroy', $facility) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
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
                            <td colspan="7" class="text-center">No facilities found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $facilities->links() }}
        </div>
    </div>
@endsection
