@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_animals'] }}</h3>
                    <p>Total Animals</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hippo"></i>
                </div>
                <a href="{{ route('admin.animals.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_facilities'] }}</h3>
                    <p>Total Facilities</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <a href="{{ route('admin.facilities.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_activities'] }}</h3>
                    <p>Total Activities</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('admin.activities.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Activity Log -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activities</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Start Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $activity)
                                <tr>
                                    <td>{{ $activity->title }}</td>
                                    <td>{{ ucfirst($activity->type) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $activity->status === 'scheduled' ? 'success' : ($activity->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($activity->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $activity->start_time ? $activity->start_time->format('M d, Y H:i') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No recent activities</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Add Forms -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.animals.create') }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-plus"></i> Add New Animal
                        </a>
                        <a href="{{ route('admin.facilities.create') }}" class="btn btn-success btn-block mb-2">
                            <i class="fas fa-plus"></i> Add New Facility
                        </a>
                        <a href="{{ route('admin.activities.create') }}" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-plus"></i> Add New Activity
                        </a>
                        <a href="{{ route('admin.map-editor.index') }}" class="btn btn-info btn-block">
                            <i class="fas fa-map"></i> Open Map Editor
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Stats</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Active Animals</span>
                            <span class="badge badge-success">{{ $stats['active_animals'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Open Facilities</span>
                            <span class="badge badge-success">{{ $stats['open_facilities'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Scheduled Activities</span>
                            <span class="badge badge-warning">{{ $stats['scheduled_activities'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
