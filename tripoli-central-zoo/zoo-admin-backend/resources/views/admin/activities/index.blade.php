@extends('layouts.admin')

@section('title', 'Activities')
@section('page-title', 'Activities Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Activities</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Activities List</h3>
            <div class="card-tools">
                <a href="{{ route('admin.activities.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Activity
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Related To</th>
                        <th>Start Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->title }}</td>
                            <td><span class="badge badge-info">{{ ucfirst($activity->type) }}</span></td>
                            <td>
                                <span class="badge badge-{{ $activity->status === 'scheduled' ? 'success' : ($activity->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </td>
                            <td>
                                @if($activity->animal)
                                    <small>Animal: {{ $activity->animal->name }}</small>
                                @elseif($activity->facility)
                                    <small>Facility: {{ $activity->facility->name }}</small>
                                @else
                                    <small class="text-muted">None</small>
                                @endif
                            </td>
                            <td>{{ $activity->start_time ? $activity->start_time->format('M d, Y H:i') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.activities.destroy', $activity) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
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
                            <td colspan="6" class="text-center">No activities found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $activities->links() }}
        </div>
    </div>
@endsection
